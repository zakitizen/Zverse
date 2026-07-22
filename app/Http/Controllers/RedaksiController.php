<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RedaksiController extends Controller
{
    // ─── Auth (deprecated — gunakan /login universal) ─────────────────────────

    public function showLogin()
    {
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('redaksi_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function dashboard()
    {
        $user     = $this->currentUser();
        $pending  = Article::where('status', 'pending')->latest()->get();
        $approved = Article::where('status', 'approved')->latest()->get();
        $all      = Article::latest()->get();
        return view('redaksi.dashboard', compact('user', 'pending', 'approved', 'all'));
    }

    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        $user = $this->currentUser();

        return view('pewarta.article-form', ['article' => $article, 'user' => $user]);
    }

    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $data = $this->validateArticle($request);

        if ($request->hasFile('image_upload')) {
            $data['image'] = $this->storeUploadedImage($request);
        }

        $article->fill(array_merge($data, [
            'slug' => $article->title === $data['title'] ? $article->slug : Article::generateSlug($data['title']),
            'author' => $article->author_name ?? $article->author,
            'source' => 'admin',
        ]));
        $article->save();

        $user = $this->currentUser();
        if ($request->input('action') === 'submit') {
            $article->approve($user->display_name);
            return redirect()->route('redaksi.dashboard')->with('success', 'Artikel berhasil disetujui.');
        }

        return redirect()->route('redaksi.dashboard')->with('success', 'Artikel berhasil diperbarui.');
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

    public function approve(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $user    = $this->currentUser();
        $article->approve($user->display_name, $request->note ?? null);
        return back()->with('success', 'Artikel disetujui.');
    }

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

    public function publish(string $id)
    {
        $article = Article::findOrFail($id);
        if (!in_array($article->status, ['approved', 'pending'])) {
            return back()->with('error', 'Artikel harus disetujui terlebih dahulu.');
        }

        $article->publish();

        return back()->with('success', 'Artikel berhasil diterbitkan!');
    }

    public function unpublish(string $id)
    {
        $article = Article::findOrFail($id);
        $article->unpublish();
        return back()->with('success', 'Artikel di-unpublish.');
    }

    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    private function currentUser(): User
    {
        if (Auth::check() && Auth::user()->role === 'redaksi') {
            return Auth::user();
        }
        abort(403, 'Anda harus login sebagai Redaksi.');
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
        $data = [
            'title' => (string) $request->input('title', ''),
            'excerpt' => (string) $request->input('excerpt', ''),
            'content' => (string) $request->input('content', ''),
            'category' => $request->input('category'),
            'image' => $request->input('image'),
            'read_time' => $request->input('read_time'),
            'tags' => $request->input('tags'),
        ];

        $data['tags'] = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('tags', ''))), fn($tag) => $tag !== ''));
        $data['read_time'] = $data['read_time'] ?? '5 menit';
        $data['image'] = $data['image'] ?? '';

        return $data;
    }
}
