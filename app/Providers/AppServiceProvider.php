<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider utama aplikasi.
 *
 * Berisi inisialisasi yang berjalan saat aplikasi di-bootstrap: memaksa
 * HTTPS di lingkungan production dan memastikan symlink storage publik ada.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan service ke container.
     */
    public function register(): void {}

    /**
     * Menjalankan inisialisasi setelah semua service terdaftar.
     */
    public function boot(): void
    {
        $this->forceHttps();
        $this->ensurePublicStorageLink();
    }

    /**
     * Paksa semua URL memakai https bila request aslinya lewat https.
     *
     * Di belakang reverse proxy / CDN (mis. Cloudflare, Railway), server asal
     * menerima koneksi http biasa sehingga Laravel menganggap request http dan
     * menghasilkan URL `http://...`. Akibatnya `fetch()` dari halaman https ke
     * URL http di-block browser sebagai mixed content ("Failed to fetch").
     *
     * Deteksi dilakukan lewat skema request langsung ATAU header
     * `X-Forwarded-Proto` yang di-set proxy. Tidak lagi bergantung pada
     * `APP_ENV` agar konsisten di semua lingkungan deployment.
     */
    protected function forceHttps(): void
    {
        $request       = $this->app['request'];
        $forwardedProto = strtolower((string) $request->header('X-Forwarded-Proto', ''));

        if (config('app.env') === 'production' || $request->isSecure() || $forwardedProto === 'https') {
            URL::forceScheme('https');
        }
    }

    /**
     * Membuat symlink `public/storage` → `storage/app/public` bila belum ada.
     *
     * Tanpa symlink ini, file yang di-upload ke disk 'public' tidak bisa
     * diakses lewat URL `/storage/...`. Jika symlink gagal dibuat (mis. karena
     * kebijakan OS), kegagalan diabaikan diam-diam — upload tetap berjalan,
     * dan fallback penyalinan manual di `HandlesArticleImages` yang menjamin
     * file tetap bisa diakses.
     */
    protected function ensurePublicStorageLink(): void
    {
        $target = storage_path('app/public');
        $link   = public_path('storage');

        if (file_exists($link) || is_link($link)) {
            return;
        }

        if (!is_dir($target)) {
            return;
        }

        try {
            symlink($target, $link);
        } catch (\Throwable $e) {
            // Abaikan bila lingkungan tidak mengizinkan pembuatan symlink.
        }
    }
}
