<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesArticleImages;
use App\Http\Controllers\Concerns\ValidatesArticleInput;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk semua aksi Pewarta (reporter).
 *
 * Pewarta adalah penulis artikel. Tanggung jawab controller ini:
 *  - Mengelola artikel miliknya sendiri (buat, edit, hapus, submit ke redaksi).
 *  - Menangani upload gambar dari editor & gambar sampul.
 *  - Logout khusus pewarta (login sudah terpusat di /login universal).
 *
 * Keamanan: semua aksi memastikan artikel yang diakses adalah milik pewarta
 * yang sedang login (author_id harus sama dengan id user aktif).
 */
class PewartaController extends Controller
{
    use HandlesArticleImages;
    use ValidatesArticleInput;

    // ─── Auth (deprecated — gunakan /login universal) ─────────────────────────

    /**
     * Menampilkan halaman login pewarta lama.
     *
     * @deprecated Semua login kini terpusat di `/login`. Route ini tinggal
     *             redirect supaya link lama tidak 404.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showLogin()
    {
        return redirect()->route('login');
    }

    /**
     * Menerima POST login pewarta lama.
     *
     * @deprecated Redirect ke halaman login universal.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        return redirect()->route('login');
    }

    /**
     * Logout pewarta: mengakhiri sesi auth + membersihkan session role.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('pewarta_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────────

    /**
     * Menampilkan dashboard pewarta: daftar seluruh artikel miliknya,
     * diurutkan dari yang terbaru.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $user     = $this->currentUser();
        $articles = Article::where('author_id', $user->id)->latest()->get();

        return view('pewarta.dashboard', compact('user', 'articles'));
    }

    // ─── Articles ──────────────────────────────────────────────────────────────

    /**
     * Menampilkan form pembuatan artikel baru (mode draft).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $user = $this->currentUser();

        return view('pewarta.article-form', ['article' => null, 'user' => $user]);
    }

    /**
     * Menyimpan artikel baru ke database.
     *
     * Artikel dibuat berstatus `draft`. Jika form dikirim dengan aksi
     * "Kirim" (`submit_to_redaksi` / `action=submit`), artikel langsung
     * diajukan ke redaksi (status menjadi `pending`).
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = $this->currentUser();
        $data = $this->validateArticle($request);

        if ($request->hasFile('image_upload')) {
            $data['image'] = $this->storeUploadedImage($request);
        }

        $submitToRedaksi = $this->wantsSubmit($request);

        $payload = array_merge($data, [
            'slug'        => Article::generateSlug($data['title']),
            'author'      => $user->display_name,
            'author_id'   => $user->id,
            'author_name' => $user->display_name,
            'source'      => 'admin',
            'status'      => 'draft',
        ]);

        $article = Article::create($payload);

        if ($submitToRedaksi) {
            $article->submitForReview();
        }

        return redirect()->route('pewarta.dashboard')->with(
            'success',
            $submitToRedaksi ? 'Artikel berhasil dikirim ke redaksi.' : 'Artikel disimpan sebagai draft.'
        );
    }

    /**
     * Menampilkan form penyuntingan artikel milik pewarta.
     *
     * Hanya artikel berstatus `draft` atau `rejected` yang bisa diedit —
     * artikel yang sudah masuk review tidak boleh diubah pewarta.
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(string $id)
    {
        $user    = $this->currentUser();
        $article = $this->ownArticle($id, $user);

        if (!in_array($article->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Artikel ini tidak bisa diedit.');
        }

        return view('pewarta.article-form', compact('article', 'user'));
    }

    /**
     * Memperbarui artikel milik pewarta.
     *
     * Jika ada file sampul baru, gambar lama diganti. Jika tidak ada file
     * baru dan field URL dikosongkan, gambar lama dipertahankan. Sama seperti
     * `store()`, artikel bisa langsung dikirim ke redaksi lewat aksi submit.
     *
     * @param Request $request
     * @param string  $id      ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $user    = $this->currentUser();
        $article = $this->ownArticle($id, $user);

        $data = $this->validateArticle($request);

        if ($request->hasFile('image_upload')) {
            $data['image'] = $this->storeUploadedImage($request);
        } elseif (empty($data['image'])) {
            // Pertahankan gambar lama saat user tidak mengganti & mengosongkan URL.
            $data['image'] = $article->image;
        }

        $submitToRedaksi = $this->wantsSubmit($request);

        $payload = array_merge($data, [
            'slug'   => $article->title === $data['title'] ? $article->slug : Article::generateSlug($data['title']),
            'author' => $user->display_name,
            'source' => 'admin',
            'status' => 'draft',
        ]);

        $article->update($payload);

        if ($submitToRedaksi) {
            $article->submitForReview();
        }

        return redirect()->route('pewarta.dashboard')->with(
            'success',
            $submitToRedaksi ? 'Artikel berhasil dikirim ke redaksi.' : 'Artikel berhasil diupdate.'
        );
    }

    /**
     * Menghapus artikel milik pewarta secara permanen.
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $user    = $this->currentUser();
        $article = $this->ownArticle($id, $user);

        $article->delete();

        return back()->with('success', 'Artikel dihapus.');
    }

    /**
     * Endpoint JSON untuk upload gambar inline dari editor markdown.
     *
     * Route ini dipanggil lewat `fetch()` pada form artikel (field `image`).
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        $this->currentUser();

        return $this->handleEditorImageUpload($request, 'image');
    }

    /**
     * Mengajukan ulang artikel ke redaksi untuk direview.
     *
     * Hanya artikel berstatus `draft` atau `rejected` yang bisa diajukan.
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(string $id)
    {
        $user    = $this->currentUser();
        $article = $this->ownArticle($id, $user);

        if (!in_array($article->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Hanya draft atau artikel yang ditolak yang bisa disubmit.');
        }

        $article->submitForReview();

        return back()->with('success', 'Artikel berhasil diajukan ke redaksi!');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Mengambil pewarta yang sedang login, atau menolak akses jika bukan.
     *
     * @return User
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 jika
     *         user belum login atau bukan berperan pewarta.
     */
    private function currentUser(): User
    {
        if (Auth::check() && Auth::user()->role === 'pewarta') {
            return Auth::user();
        }

        abort(403, 'Anda harus login sebagai Pewarta.');
    }

    /**
     * Mengambil artikel milik user tertentu, 404 bila bukan miliknya.
     *
     * @param string $id   ID artikel.
     * @param User   $user Pewarta pemilik.
     *
     * @return Article
     */
    private function ownArticle(string $id, User $user): Article
    {
        return Article::where('id', $id)->where('author_id', $user->id)->firstOrFail();
    }

    /**
     * Menentukan apakah form artikel dikirim untuk diajukan ke redaksi.
     *
     * @param Request $request
     *
     * @return bool `true` jika user memilih aksi "Kirim".
     */
    private function wantsSubmit(Request $request): bool
    {
        return $request->boolean('submit_to_redaksi') || $request->input('action') === 'submit';
    }
}
