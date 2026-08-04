<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller untuk komentar artikel (tambah, balas, edit, hapus, muat balasan).
 *
 * Semua method memeriksa status login dan kepemilikan. Response dipilih
 * otomatis: JSON bila request berasal dari fetch/JS (`expectsJson()`),
 * atau redirect/flash biasa bila dari form konvensional.
 */
class CommentController extends Controller
{
    /**
     * Menambahkan komentar baru pada artikel.
     *
     * Bisa berupa komentar utama (tanpa `parent_id`) atau balasan
     * (dengan `parent_id` menunjuk ke komentar utama).
     *
     * @param Request $request
     * @param Article $article
     *
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request, Article $article)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        $data    = $this->validateComment($request, $article);
        $comment = $this->createComment($request, $article, $data);

        return $this->respondWithComment($comment, 'Komentar berhasil dikirim.');
    }

    /**
     * Membalas komentar yang sudah ada (route terpisah `article.comments.reply`).
     *
     * @param Request $request
     * @param Article $article
     * @param Comment $comment Komentar yang dibalas.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function reply(Request $request, Article $article, Comment $comment)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        $data    = $this->validateComment($request, $article, $comment);
        $reply   = $this->createComment($request, $article, $data, $comment);

        return $this->respondWithComment($reply, 'Balasan berhasil dikirim.');
    }

    /**
     * Mengubah isi komentar milik user yang sedang login.
     *
     * @param Request $request
     * @param Article $article
     * @param Comment $comment
     *
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, Article $article, Comment $comment)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->respondForbidden();
        }

        $validated = $this->validateComment($request, $article);
        $content   = $this->normalizeContent($validated['content'], $validated['reply_to_user_id'] ?? null);

        $comment->forceFill([
            'content' => $content,
            'body'    => $content,
        ])->save();

        $comment->load(['user', 'replyUser', 'replies' => function ($query) {
            $query->with(['user', 'replyUser']);
        }]);

        return $this->respondWithComment($comment, 'Komentar berhasil diperbarui.');
    }

    /**
     * Menghapus komentar milik user yang sedang login (soft delete).
     *
     * @param Request $request
     * @param Article $article
     * @param Comment $comment
     *
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, Article $article, Comment $comment)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->respondForbidden();
        }

        $comment->delete();

        $comment->load(['user', 'replyUser', 'replies' => function ($query) {
            $query->with(['user', 'replyUser']);
        }]);

        return $this->respondWithComment($comment, 'Komentar berhasil dihapus.', true);
    }

    /**
     * Mengambil balasan-balasan sebuah komentar (dipakai saat tombol "Lihat balasan").
     *
     * @param Request $request
     * @param Article $article
     * @param Comment $comment
     *
     * @return JsonResponse Berisi HTML card komentar + array balasan.
     */
    public function loadReplies(Request $request, Article $article, Comment $comment): JsonResponse
    {
        $replies = $comment->replies()->with(['user', 'replyUser'])->get();

        return response()->json([
            'html'    => view('partials.comment-card', ['comment' => $comment->load(['user', 'replyUser'])])->render(),
            'replies' => $replies->map(fn (Comment $reply) => [
                'id'         => $reply->id,
                'content'    => $reply->content,
                'created_at' => $reply->created_at->toIso8601String(),
            ])->values(),
        ]);
    }

    /**
     * Validasi isi komentar.
     *
     * Isi diambil dari field `content` atau `body` (fallback), di-trim, harus
     * tidak kosong dan maksimal 1000 karakter. Jika `parent_id` terisi, dipastikan
     * komentar induk benar-benar ada di artikel yang sama.
     *
     * @param Request      $request
     * @param Article      $article
     * @param Comment|null $parent Komentar induk (saat membalas).
     *
     * @return array{content: string, reply_to_user_id: mixed, parent_id: mixed}
     *
     * @throws \InvalidArgumentException Jika komentar kosong/terlalu panjang/induk tidak ditemukan.
     */
    private function validateComment(Request $request, Article $article, ?Comment $parent = null): array
    {
        $content = (string) ($request->input('content', $request->input('body', '')));
        $content = trim($content);

        if ($content === '') {
            throw new \InvalidArgumentException('Komentar tidak boleh kosong.');
        }

        if (mb_strlen($content) > 1000) {
            throw new \InvalidArgumentException('Komentar terlalu panjang.');
        }

        if ($request->filled('parent_id')) {
            $parentComment = Comment::where('article_id', $article->id)->find($request->input('parent_id'));

            if (!$parentComment) {
                throw new \InvalidArgumentException('Komentar yang ingin dibalas tidak ditemukan.');
            }
        }

        return [
            'content'         => $content,
            'reply_to_user_id' => $request->input('reply_to_user_id'),
            'parent_id'        => $request->input('parent_id'),
        ];
    }

    /**
     * Membuat record komentar di database.
     *
     * @param Request      $request
     * @param Article      $article
     * @param array        $data   Hasil validasi (`content`, `parent_id`, `reply_to_user_id`).
     * @param Comment|null $parent Komentar induk (saat membalas).
     *
     * @return Comment Komentar yang sudah di-load beserta relasinya.
     */
    private function createComment(Request $request, Article $article, array $data, ?Comment $parent = null): Comment
    {
        $user    = Auth::user();
        $content = $this->normalizeContent($data['content'], $data['reply_to_user_id'] ?? null);

        $comment = Comment::create([
            'article_id'      => $article->id,
            'user_id'         => $user->id,
            'parent_id'       => $parent?->id ?? ($data['parent_id'] ? (int) $data['parent_id'] : null),
            'reply_to_user_id' => $this->resolveReplyTargetUserId($request, $parent, $data),
            'author_name'     => $user->display_name ?? $user->username ?? 'Anonim',
            'avatar_color'    => $user->avatar_color ?? 'from-orange-500 to-amber-400',
            'content'         => $content,
            'body'            => $content,
        ]);

        return $comment->load(['user', 'replyUser', 'replies' => function ($query) {
            $query->with(['user', 'replyUser']);
        }]);
    }

    /**
     * Menentukan user yang menjadi target balasan (`reply_to_user_id`).
     *
     * Prioritas: nilai eksplisit dari request → pemilik komentar induk.
     *
     * @param Request      $request
     * @param Comment|null $parent
     * @param array        $data
     *
     * @return int|null ID user target balasan, atau `null` jika tidak ada.
     */
    private function resolveReplyTargetUserId(Request $request, ?Comment $parent, array $data): ?int
    {
        if ($request->filled('reply_to_user_id')) {
            return (int) $request->input('reply_to_user_id');
        }

        if ($parent) {
            return $parent->user_id;
        }

        if (!empty($data['parent_id'])) {
            $parentComment = Comment::find((int) $data['parent_id']);

            return $parentComment?->user_id;
        }

        return null;
    }

    /**
     * Membersihkan isi komentar.
     *
     * Menghapus awalan mention (`@username`) bila komentar adalah balasan
     * kepada user tertentu, lalu me-return isi yang sudah di-trim.
     *
     * @param string   $content       Isi komentar mentah.
     * @param int|null $replyToUserId ID user yang dibalas (jika ada).
     *
     * @return string Isi komentar yang sudah dibersihkan.
     */
    private function normalizeContent(string $content, ?int $replyToUserId = null): string
    {
        $trimmed = trim($content);

        if ($replyToUserId) {
            $trimmed = preg_replace('/^@[^\s]+\s*/', '', $trimmed) ?? $trimmed;
        }

        return trim($trimmed);
    }

    /**
     * Menyusun response sukses untuk operasi komentar.
     *
     * @param Comment $comment Komentar terkait.
     * @param string  $message Pesan sukses.
     * @param bool    $deleted Apakah ini operasi hapus.
     *
     * @return JsonResponse|RedirectResponse
     */
    private function respondWithComment(Comment $comment, string $message, bool $deleted = false)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'html'    => view('partials.comment-card', ['comment' => $comment])->render(),
                'deleted' => $deleted,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Response ketika user belum login.
     *
     * @return JsonResponse|RedirectResponse
     */
    private function respondUnauthorized()
    {
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }

        return redirect()->route('login')->withErrors(['comment' => 'Silakan login terlebih dahulu.']);
    }

    /**
     * Response ketika user tidak berhak melakukan aksi (bukan pemilik komentar).
     *
     * @return JsonResponse|RedirectResponse
     */
    private function respondForbidden()
    {
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk melakukan aksi ini.'], 403);
        }

        return back()->withErrors(['comment' => 'Anda tidak memiliki akses untuk melakukan aksi ini.']);
    }
}
