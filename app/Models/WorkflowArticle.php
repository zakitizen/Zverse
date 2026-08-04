<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model WorkflowArticle — representasi lama alur kerja artikel.
 *
 * @deprecated Model ini tidak lagi dipakai oleh controller/view manapun.
 *             Seluruh alur kerja sekarang ditangani langsung oleh model
 *             `Article` (status draft → pending → approved → published,
 *             beserta rejected/withdrawn). Model ini dipertahankan hanya
 *             agar tidak merusak referensi/migrasi lama.
 */
class WorkflowArticle extends Model
{
    /** Kolom yang boleh diisi massal (mass assignment). */
    protected $fillable = [
        'title', 'excerpt', 'content', 'category', 'image',
        'read_time', 'rating', 'featured', 'tags',
        'author_id', 'author_name',
        'status', 'submitted_at', 'reviewed_at', 'reviewed_by',
        'review_note', 'published_article_id',
    ];

    /** Konversi tipe otomatis. */
    protected $casts = [
        'tags'         => 'array',
        'featured'     => 'boolean',
        'rating'       => 'float',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    /** Label status (tidak termasuk 'withdrawn'). */
    public static array $statusLabel = [
        'draft'     => 'Draft',
        'pending'   => 'Menunggu Review',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        'published' => 'Tayang',
    ];

    /** Kelas CSS badge untuk setiap status. */
    public static array $statusColor = [
        'draft'     => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'approved'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
        'published' => 'bg-green-50 text-green-700 border-green-200',
    ];

    /**
     * Relasi: penulis artikel.
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Relasi: artikel publik terkait setelah artikel dipublikasikan.
     *
     * @return BelongsTo
     */
    public function publishedArticle(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'published_article_id');
    }

    /**
     * Accessor: label status dalam bahasa Indonesia.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    /**
     * Accessor: kelas CSS badge status.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return self::$statusColor[$this->status] ?? '';
    }

    /**
     * Memformat field tanggal (dari cast datetime) ke format "dd Mon yyyy, HH:mm".
     *
     * @param string|null $field Nama atribut tanggal (mis. `submitted_at`).
     *
     * @return string Tanggal terformat, atau "-" bila kosong.
     */
    public function formatDate(?string $field): string
    {
        if (!$this->$field) {
            return '-';
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $d = $this->$field;

        return sprintf('%02d %s %d, %02d:%02d', $d->day, $months[$d->month - 1], $d->year, $d->hour, $d->minute);
    }
}
