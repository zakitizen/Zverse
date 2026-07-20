<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PewartaController extends Controller
{
    // ─── Auth (deprecated — gunakan /login universal) ─────────────────────────

    public function showLogin()
    {
        // Redirect ke login universal
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('pewarta_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $user     = $this->currentUser();
        $articles = Article::where('author_id', $user->id)->latest()->get();
        return view('pewarta.dashboard', compact('user', 'articles'));
    }

    // ─── Articles ──────────────────────────────────────────────────────────────

    public function create()
    {
        $user = $this->currentUser();
        return view('pewarta.article-form', ['article' => null, 'user' => $user]);
    }

    public function store(Request $request)
    {
        $user = $this->currentUser();
        $data = $this->validateArticle($request);

        if ($request->hasFile('image_upload')) {
            $data['image'] = $this->storeUploadedImage($request);
        }

        $submitToRedaksi = $request->boolean('submit_to_redaksi') || $request->input('action') === 'submit';
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

        return redirect()->route('pewarta.dashboard')->with('success', $submitToRedaksi ? 'Artikel berhasil dikirim ke redaksi.' : 'Artikel disimpan sebagai draft.');
    }

    public function edit(string $id)
    {
        $user    = $this->currentUser();
        $article = Article::where('id', $id)->where('author_id', $user->id)->firstOrFail();
        if (!in_array($article->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Artikel ini tidak bisa diedit.');
        }
        return view('pewarta.article-form', compact('article', 'user'));
    }

    public function update(Request $request, string $id)
    {
        $user    = $this->currentUser();
        $article = Article::where('id', $id)->where('author_id', $user->id)->firstOrFail();

        $data = $this->validateArticle($request);
        if ($request->hasFile('image_upload')) {
            $data['image'] = $this->storeUploadedImage($request);
        } elseif (empty($data['image'])) {
            $data['image'] = $article->image;
        }

        $submitToRedaksi = $request->boolean('submit_to_redaksi') || $request->input('action') === 'submit';
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
        return redirect()->route('pewarta.dashboard')->with('success', $submitToRedaksi ? 'Artikel berhasil dikirim ke redaksi.' : 'Artikel berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $user    = $this->currentUser();
        $article = Article::where('id', $id)->where('author_id', $user->id)->firstOrFail();
        $article->delete();
        return back()->with('success', 'Artikel dihapus.');
    }

    public function uploadImage(Request $request)
    {
        $this->currentUser();

        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['message' => 'File gambar tidak valid.'], 422);
        }

        $path = $request->file('image')->store('articles', 'public');
        $publicPath = public_path('storage/' . $path);
        $storedPath = storage_path('app/public/' . $path);

        if (file_exists($storedPath) && !file_exists($publicPath)) {
            $directory = dirname($publicPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            copy($storedPath, $publicPath);
        }

        return response()->json([
            'url' => asset('storage/' . $path),
        ]);
    }

    public function submit(string $id)
    {
        $user    = $this->currentUser();
        $article = Article::where('id', $id)->where('author_id', $user->id)->firstOrFail();
        if (!in_array($article->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Hanya draft atau artikel yang ditolak yang bisa disubmit.');
        }
        $article->submitForReview();
        return back()->with('success', 'Artikel berhasil diajukan ke redaksi!');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function currentUser(): User
    {
        if (Auth::check() && Auth::user()->role === 'pewarta') {
            return Auth::user();
        }
        abort(403, 'Anda harus login sebagai Pewarta.');
    }

    private function storeUploadedImage(Request $request): string
    {
        $file = $request->file('image_upload');
        $path = $file->store('articles', 'public');

        $storedPath = storage_path('app/public/' . $path);
        $publicPath = public_path('storage/' . $path);

        if (file_exists($storedPath) && !file_exists($publicPath)) {
            $directory = dirname($publicPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            copy($storedPath, $publicPath);
        }

        return asset('storage/' . $path);
    }

    private function validateArticle(Request $request): array
    {
        $errors = [];
        $data = [
            'title'    => (string) $request->input('title', ''),
            'excerpt'  => (string) $request->input('excerpt', ''),
            'content'  => (string) $request->input('content', ''),
            'category' => $request->input('category'),
            'image'    => $request->input('image'),
            'read_time' => $request->input('read_time'),
            'tags'     => $request->input('tags'),
        ];

        if (blank($data['title'])) {
            $errors['title'] = ['Judul artikel wajib diisi.'];
        }

        if (blank($data['excerpt'])) {
            $errors['excerpt'] = ['Ringkasan artikel wajib diisi.'];
        }

        if (blank($data['content'])) {
            $errors['content'] = ['Konten artikel wajib diisi.'];
        }

        if (!in_array($data['category'], ['games', 'musik', 'film', 'entertainment'], true)) {
            $errors['category'] = ['Kategori tidak valid.'];
        }

        if (!blank($data['image']) && !filter_var($data['image'], FILTER_VALIDATE_URL)) {
            $errors['image'] = ['URL gambar tidak valid.'];
        }

        if (!blank($data['read_time']) && strlen((string) $data['read_time']) > 50) {
            $errors['read_time'] = ['Estimasi baca terlalu panjang.'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        $data['tags'] = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('tags', ''))), fn($tag) => $tag !== ''));
        $data['read_time'] = $data['read_time'] ?? '5 menit';
        $data['image'] = $data['image'] ?? '';

        return $data;
    }
}
