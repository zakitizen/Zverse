<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

/**
 * Trait untuk menangani semua operasi upload gambar artikel.
 *
 * Dipakai bersama oleh PewartaController dan RedaksiController agar logika
 * upload (validasi tipe/ukuran, simpan ke disk publik, buat symlink jika
 * belum ada) hanya ditulis SATU KALI, sehingga tidak ada duplikasi kode
 * yang berisiko menyimpang antar controller.
 *
 * Alur penyimpanan:
 *  1. File di-validasi (harus gambar, maksimal 5MB).
 *  2. File disimpan ke `storage/app/public/articles/` via disk 'public'.
 *  3. Jika belum ada salinan di `public/storage/articles/`, dibuat salinan
 *     manual — ini mengamankan kasus di mana symlink storage tidak tersedia.
 *  4. URL publik yang siap dipakai diambil lewat `asset('storage/...')`.
 */
trait HandlesArticleImages
{
    /** Ukuran maksimal file gambar (dalam byte). */
    protected int $maxImageSize = 5 * 1024 * 1024; // 5MB

    /** Ekstensi file yang diizinkan (lowercase). */
    protected array $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /** MIME type yang diizinkan. */
    protected array $allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Handler untuk upload gambar inline dari editor (fetch/JS).
     *
     * Membaca file dari field `$field` (default: `image`), menyimpannya,
     * lalu mengembalikan JSON berisi URL publik.
     *
     * @param Request $request Request multipart yang memuat file.
     * @param string  $field   Nama field file pada request.
     *
     * @return JsonResponse `{url: string}` jika sukses (200),
     *                      `{message: string}` jika file tidak valid (422).
     */
    public function handleEditorImageUpload(Request $request, string $field = 'image'): JsonResponse
    {
        $file = $request->file($field);

        // Cek keberadaan file + kevalidan upload (misal tidak terpotong).
        if (!$file || !$file->isValid()) {
            return response()->json(['message' => 'File gambar tidak valid.'], 422);
        }

        $error = $this->validateImageFile($file);
        if ($error !== null) {
            return response()->json(['message' => $error], 422);
        }

        $path = $file->store('articles', 'public');
        $this->persistImageToPublicDisk($path);

        return response()->json(['url' => asset('storage/' . $path)]);
    }

    /**
     * Simpan file gambar sampul artikel yang dikirim lewat form biasa.
     *
     * Dipakai untuk field `image_upload` pada form pembuatan/penyuntingan
     * artikel. Jika file tidak valid, melempar ValidationException agar
     * error tampil sebagai pesan validasi di form.
     *
     * @param Request $request Request multipart yang memuat file.
     * @param string  $field   Nama field file pada request.
     *
     * @return string URL publik gambar yang tersimpan (mis. `http://.../storage/articles/xxx.png`).
     *
     * @throws ValidationException Jika file bukan gambar atau melebihi batas ukuran.
     */
    protected function storeUploadedImage(Request $request, string $field = 'image_upload'): string
    {
        $file = $request->file($field);

        if (!$file || !$file->isValid()) {
            throw ValidationException::withMessages([$field => ['File gambar tidak valid.']]);
        }

        $error = $this->validateImageFile($file);
        if ($error !== null) {
            throw ValidationException::withMessages([$field => [$error]]);
        }

        $path = $file->store('articles', 'public');
        $this->persistImageToPublicDisk($path);

        return asset('storage/' . $path);
    }

    /**
     * Validasi bahwa file upload benar-benar gambar dengan ukuran wajar.
     *
     * Pengecekan dilakukan lewat ekstensi ORISINAL + MIME type untuk menolak
     * file berbahaya (mis. `.php`) yang bisa dieksekusi bila tersimpan di
     * direktori yang bisa diakses web.
     *
     * @param UploadedFile $file File yang akan divalidasi.
     *
     * @return string|null Pesan error, atau `null` jika lolos semua validasi.
     */
    protected function validateImageFile(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime      = $file->getMimeType();

        if (!in_array($extension, $this->allowedImageExtensions, true) || !in_array($mime, $this->allowedImageMimes, true)) {
            return 'File harus berupa gambar (JPG, PNG, WebP, atau GIF).';
        }

        if ($file->getSize() > $this->maxImageSize) {
            return 'Ukuran gambar maksimal 5MB.';
        }

        return null;
    }

    /**
     * Pastikan file yang sudah tersimpan di disk internal juga tersedia di
     * direktori publik (`public/storage/articles/`).
     *
     * Normalnya `public/storage` adalah symlink ke `storage/app/public`.
     * Jika symlink tidak ada, kita buat salinan file secara manual agar
     * gambar tetap bisa diakses lewat URL publik.
     *
     * @param string $path Path relatif file di dalam disk 'public'.
     */
    protected function persistImageToPublicDisk(string $path): void
    {
        $storedPath = storage_path('app/public/' . $path);
        $publicPath = public_path('storage/' . $path);

        if (file_exists($storedPath) && !file_exists($publicPath)) {
            $directory = dirname($publicPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            copy($storedPath, $publicPath);
        }
    }
}
