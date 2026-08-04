<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Trait untuk validasi input artikel — dipakai bersama oleh PewartaController
 * dan RedaksiController.
 *
 * Sebelumnya validasi hanya ada di PewartaController, sehingga Redaksi bisa
 * menyimpan artikel dengan judul/ringkasan/konten kosong. Dengan trait ini
 * kedua peran memakai aturan validasi yang sama (single source of truth).
 *
 * Catatan: validasi sengaja ditulis manual (bukan `$request->validate()`)
 * agar tidak bergantung pada package translator/terjemahan Laravel.
 */
trait ValidatesArticleInput
{
    /** Kategori artikel yang valid di aplikasi ini. */
    protected array $validArticleCategories = ['games', 'musik', 'film', 'entertainment'];

    /**
     * Validasi dan normalisasi data artikel dari request form.
     *
     * Memeriksa: judul, ringkasan, dan konten wajib diisi; kategori harus
     * salah satu yang valid; URL gambar (jika diisi) harus URL sah; estimasi
     * baca tidak boleh terlalu panjang. Data yang dikembalikan sudah
     * dibersihkan (trim) dan di-normalisasi (tags menjadi array, default
     * `read_time` & `image`).
     *
     * @param Request $request Request form pembuatan/penyuntingan artikel.
     *
     * @return array<string, mixed> Data artikel yang sudah tervalidasi:
     *                              title, excerpt, content, category, image,
     *                              read_time, tags (array).
     *
     * @throws ValidationException Jika ada field yang tidak valid.
     */
    protected function validateArticle(Request $request): array
    {
        $errors = [];
        $data = [
            'title'     => trim((string) $request->input('title', '')),
            'excerpt'   => trim((string) $request->input('excerpt', '')),
            'content'   => trim((string) $request->input('content', '')),
            'category'  => $request->input('category'),
            'image'     => trim((string) $request->input('image', '')),
            'read_time' => $request->input('read_time'),
            'tags'      => $request->input('tags'),
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

        if (!in_array($data['category'], $this->validArticleCategories, true)) {
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

        // Normalisasi: tags string "a, b" → array ['a','b'] (hapus yang kosong/duplikat).
        $data['tags'] = array_values(array_unique(array_filter(
            array_map('trim', explode(',', (string) $request->input('tags', ''))),
            fn ($tag) => $tag !== ''
        )));

        $data['read_time'] = $data['read_time'] ?: '5 menit';
        $data['image']     = $data['image'] ?: '';

        return $data;
    }
}
