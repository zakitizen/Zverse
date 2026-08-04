<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Article — merepresentasikan satu artikel di portal Zverse.
 *
 * Artikel memiliki alur status yang berurutan:
 * `draft` → `pending` → `approved` → `published` (atau `rejected`/`withdrawn`).
 * Transisi status diatur lewat method `submitForReview()`, `approve()`,
 * `reject()`, `publish()`, dan `unpublish()`.
 */
class Article extends Model
{
    /** Kolom yang boleh diisi massal (mass assignment). */
    protected $fillable = [
        'slug', 'title', 'excerpt', 'content', 'category',
        'image', 'author', 'read_time', 'rating', 'featured',
        'tags', 'source', 'status', 'author_id', 'author_name',
        'submitted_at', 'reviewed_at', 'reviewed_by', 'review_note',
        'published_article_id',
    ];

    /** Konversi tipe otomatis untuk atribut tertentu. */
    protected $casts = [
        'featured'     => 'boolean',
        'tags'         => 'array',
        'rating'       => 'float',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    /** Label (teks) untuk setiap status — dipakai di tampilan. */
    public static array $statusLabel = [
        'draft'     => 'Draft',
        'pending'   => 'Menunggu Review',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        'published' => 'Tayang',
        'withdrawn' => 'Ditarik',
    ];

    /** Kelas CSS (badge) untuk setiap status — dipakai di tampilan. */
    public static array $statusColor = [
        'draft'     => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'approved'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
        'published' => 'bg-green-50 text-green-700 border-green-200',
        'withdrawn' => 'bg-orange-50 text-orange-700 border-orange-200',
    ];

    /** Metadata kategori: label, ikon, warna, dan deskripsi untuk tampilan. */
    public static array $categoryMeta = [
        'games'         => ['label' => 'Games',         'icon' => '🎮', 'color' => 'text-emerald-600', 'bgColor' => 'bg-emerald-500', 'description' => 'Review, berita, dan panduan untuk semua platform gaming.'],
        'musik'         => ['label' => 'Musik',         'icon' => '🎵', 'color' => 'text-purple-600',  'bgColor' => 'bg-purple-500',  'description' => 'Album baru, ulasan konser, dan tren musik global & lokal.'],
        'film'          => ['label' => 'Film',          'icon' => '🎬', 'color' => 'text-blue-600',    'bgColor' => 'bg-blue-500',    'description' => 'Review film, festival internasional, dan berita industri perfilman.'],
        'entertainment' => ['label' => 'Entertainment', 'icon' => '✨', 'color' => 'text-orange-600',  'bgColor' => 'bg-orange-500',  'description' => 'Pop culture, streaming, anime, dan semua hal hiburan lainnya.'],
    ];

    /**
     * Accessor: metadata kategori artikel (dari `$categoryMeta`).
     *
     * Dipakai di tampilan sebagai `$article->category_meta` → array berisi
     * label, icon, color, bgColor, description. Kembalikan array kosong jika
     * kategori tidak dikenal.
     *
     * @return array<string, mixed>
     */
    public function getCategoryMetaAttribute(): array
    {
        return self::$categoryMeta[$this->category] ?? [];
    }

    /**
     * Relasi: komentar utama (bukan balasan) pada artikel ini.
     *
     * Balasan disimpan dengan `parent_id` terisi, jadi di sini difilter
     * `parent_id IS NULL` dan diurutkan dari yang terbaru.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    /**
     * Relasi: penulis (user) artikel ini.
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Accessor: label status dalam bahasa Indonesia (mis. "Tayang").
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    /**
     * Accessor: kelas CSS badge status untuk tampilan.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return self::$statusColor[$this->status] ?? '';
    }

    /**
     * Accessor: URL gambar yang siap ditampilkan.
     *
     * Gambar bisa berupa URL eksternal (http...), path relatif publik
     * (`storage/...` atau `/storage/...`), atau kosong.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return '';
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        if (str_starts_with($this->image, '/storage/')) {
            return asset(ltrim($this->image, '/'));
        }

        return $this->image;
    }

    /**
     * Transisi status: mengajukan artikel ke redaksi untuk direview.
     *
     * Hanya boleh dilakukan pada status `draft` atau `rejected`. Jika sukses,
     * status menjadi `pending` dan `submitted_at` diisi waktu sekarang.
     *
     * @return bool `true` jika berhasil, `false` jika status tidak memenuhi syarat.
     */
    public function submitForReview(): bool
    {
        if (!in_array($this->status, ['draft', 'rejected'])) {
            return false;
        }

        $this->fill([
            'status'       => 'pending',
            'submitted_at' => now(),
        ])->save();

        return true;
    }

    /**
     * Transisi status: menyetujui artikel setelah review redaksi.
     *
     * Status menjadi `approved`, mencatat siapa reviewer, waktu review, dan
     * catatan opsional.
     *
     * @param string      $reviewerName Nama redaksi yang menyetujui.
     * @param string|null $note         Catatan/komentar review (opsional).
     *
     * @return bool Selalu `true` setelah berhasil disimpan.
     */
    public function approve(string $reviewerName, ?string $note = null): bool
    {
        $this->fill([
            'status'       => 'approved',
            'reviewed_at'  => now(),
            'reviewed_by'  => $reviewerName,
            'review_note'  => $note,
        ])->save();

        return true;
    }

    /**
     * Transisi status: menolak artikel beserta alasan.
     *
     * Status menjadi `rejected`; artikel kembali ke pewarta untuk diperbaiki.
     *
     * @param string $reviewerName Nama redaksi yang menolak.
     * @param string $reason       Alasan penolakan.
     *
     * @return bool Selalu `true` setelah berhasil disimpan.
     */
    public function reject(string $reviewerName, string $reason): bool
    {
        $this->fill([
            'status'       => 'rejected',
            'reviewed_at'  => now(),
            'reviewed_by'  => $reviewerName,
            'review_note'  => $reason,
        ])->save();

        return true;
    }

    /**
     * Transisi status: menerbitkan artikel agar tampil di halaman publik.
     *
     * Hanya boleh dilakukan pada status `approved` atau `pending`. Status
     * menjadi `published`; slug dibuatkan bila belum ada.
     *
     * @return bool `true` jika berhasil, `false` jika status tidak memenuhi syarat.
     */
    public function publish(): bool
    {
        if (!in_array($this->status, ['approved', 'pending'])) {
            return false;
        }

        $this->fill([
            'slug'        => $this->slug ?: self::generateSlug($this->title),
            'status'      => 'published',
            'reviewed_at' => now(),
        ])->save();

        return true;
    }

    /**
     * Transisi status: menarik artikel dari tampilan publik.
     *
     * Status menjadi `withdrawn` dan tautan ke artikel publik dihapus.
     *
     * @return bool Selalu `true` setelah berhasil disimpan.
     */
    public function unpublish(): bool
    {
        $this->fill([
            'status'              => 'withdrawn',
            'published_article_id' => null,
        ])->save();

        return true;
    }

    /**
     * Membuat slug unik dari judul artikel.
     *
     * Jika slug sudah dipakai artikel lain, diberikan akhiran angka
     * (`-1`, `-2`, dst.) sampai ditemukan slug yang tersedia.
     *
     * @param string $title Judul artikel.
     *
     * @return string Slug yang dijamin unik di tabel articles.
     */
    public static function generateSlug(string $title): string
    {
        $slug    = Str::slug($title);
        $original = $slug;
        $count    = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
