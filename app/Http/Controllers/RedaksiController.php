<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesArticleImages;
use App\Http\Controllers\Concerns\ValidatesArticleInput;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk semua aksi Redaksi (editor/pemimpin redaksi).
 *
 * Redaksi bertugas meninjau artikel yang dikirim pewarta:
 *  - Menyetujui (approve), menolak (reject), menerbitkan (publish),
 *    menarik kembali (unpublish), dan menghapus artikel.
 *  - Menyunting langsung isi artikel lewat form yang sama dengan pewarta.
 *
 * Berbeda dengan PewartaController, redaksi bisa mengakses SEMUA artikel,
 * bukan hanya miliknya sendiri.
 */
class RedaksiController extends Controller
{
    use HandlesArticleImages;
    use ValidatesArticleInput;

    // ─── Auth (deprecated — gunakan /login universal) ─────────────────────────

    /**
     * Menampilkan halaman login redaksi lama.
     *
     * @deprecated Semua login kini terpusat di `/login`.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showLogin()
    {
        return redirect()->route('login');
    }

    /**
     * Menerima POST login redaksi lama.
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
     * Logout redaksi: mengakhiri sesi auth + membersihkan session role.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('redaksi_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────────

    /**
     * Menampilkan dashboard redaksi dengan 3 daftar artikel:
     *  - `pending`  : menunggu review (perlu disetujui/ditolak).
     *  - `approved` : sudah disetujui, siap diterbitkan.
     *  - `all`      : seluruh artikel beserta statusnya (alur kerja penuh).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $user     = $this->currentUser();
        $pending  = Article::where('status', 'pending')->latest()->get();
        $approved = Article::where('status', 'approved')->latest()->get();
        $all      = Article::latest()->get();

        return view('redaksi.dashboard', compact('user', 'pending', 'approved', 'all'));
    }

    // ─── Articles ──────────────────────────────────────────────────────────────

    /**
     * Menampilkan form penyuntingan artikel (bisa diakses untuk semua status).
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        $user    = $this->currentUser();

        return view('pewarta.article-form', ['article' => $article, 'user' => $user]);
    }

    /**
     * Memperbarui artikel hasil suntingan redaksi.
     *
     * Jika dikirim dengan aksi "Setujui" (`action=submit`), artikel langsung
     * disetujui oleh redaksi yang sedang login (status → `approved`).
     *
     * @param Request $request
     * @param string  $id      ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $user    = $this->currentUser();
        $data    = $this->validateArticle($request);

        if ($request->hasFile('image_upload')) {
            $data['image'] = $this->storeUploadedImage($request);
        } elseif (empty($data['image'])) {
            // Konsisten dengan pewarta: pertahankan gambar lama bila dikosongkan.
            $data['image'] = $article->image;
        }

        $article->fill(array_merge($data, [
            'slug'   => $article->title === $data['title'] ? $article->slug : Article::generateSlug($data['title']),
            'source' => 'admin',
        ]));
        $article->save();

        if ($request->input('action') === 'submit') {
            $article->approve($user->display_name);

            return redirect()->route('redaksi.dashboard')->with('success', 'Artikel berhasil disetujui.');
        }

        return redirect()->route('redaksi.dashboard')->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Endpoint JSON untuk upload gambar inline dari editor (sama seperti pewarta).
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
     * Menyetujui artikel sehingga siap diterbitkan.
     *
     * @param Request $request
     * @param string  $id      ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $user    = $this->currentUser();

        $article->approve($user->display_name, $request->note ?? null);

        return back()->with('success', 'Artikel disetujui.');
    }

    /**
     * Menolak artikel beserta alasan (wajib diisi, maksimal 500 karakter).
     * Artikel yang ditolak kembali ke pewarta untuk diperbaiki.
     *
     * @param Request $request
     * @param string  $id      ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, string $id)
    {
        $reason = trim((string) $request->input('reason', ''));

        if (empty($reason)) {
            return back()->withErrors(['reason' => 'Alasan penolakan wajib diisi.']);
        }

        if (strlen($reason) > 500) {
            return back()->withErrors(['reason' => 'Alasan penolakan terlalu panjang (max 500 karakter).']);
        }

        $article = Article::findOrFail($id);
        $user    = $this->currentUser();

        if (!in_array($article->status, ['pending', 'approved', 'draft'])) {
            return back()->with('error', 'Artikel ini tidak bisa ditolak pada status saat ini.');
        }

        $article->reject($user->display_name, $reason);

        return back()->with('success', 'Artikel ditolak.');
    }

    /**
     * Menerbitkan artikel sehingga tampil di halaman publik.
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish(string $id)
    {
        $article = Article::findOrFail($id);

        if (!in_array($article->status, ['approved', 'pending'])) {
            return back()->with('error', 'Artikel harus disetujui terlebih dahulu.');
        }

        $article->publish();

        return back()->with('success', 'Artikel berhasil diterbitkan!');
    }

    /**
     * Menarik artikel dari tampilan publik (status → `withdrawn`).
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unpublish(string $id)
    {
        $article = Article::findOrFail($id);

        $article->unpublish();

        return back()->with('success', 'Artikel di-unpublish.');
    }

    /**
     * Menghapus artikel secara permanen dari sistem.
     *
     * @param string $id ID artikel.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Mengambil redaksi yang sedang login, atau menolak akses jika bukan.
     *
     * @return User
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 jika
     *         user belum login atau bukan berperan redaksi.
     */
    private function currentUser(): User
    {
        if (Auth::check() && Auth::user()->role === 'redaksi') {
            return Auth::user();
        }

        abort(403, 'Anda harus login sebagai Redaksi.');
    }
}
