<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'content', 'category',
        'image', 'author', 'read_time', 'rating', 'featured',
        'tags', 'source', 'likes', 'status', 'author_id', 'author_name',
        'submitted_at', 'reviewed_at', 'reviewed_by', 'review_note',
        'published_article_id',
    ];

    protected $casts = [
        'featured'     => 'boolean',
        'tags'         => 'array',
        'rating'       => 'float',
        'likes'        => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public static array $statusLabel = [
        'draft'     => 'Draft',
        'pending'   => 'Menunggu Review',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        'published' => 'Tayang',
        'withdrawn' => 'Ditarik',
    ];

    public static array $statusColor = [
        'draft'     => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'approved'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
        'published' => 'bg-green-50 text-green-700 border-green-200',
        'withdrawn' => 'bg-orange-50 text-orange-700 border-orange-200',
    ];

    // ─── Category Meta ────────────────────────────────────────────────────────
    public static array $categoryMeta = [
        'games'         => ['label' => 'Games',         'icon' => '🎮', 'color' => 'text-emerald-600', 'bgColor' => 'bg-emerald-500', 'description' => 'Review, berita, dan panduan untuk semua platform gaming.'],
        'musik'         => ['label' => 'Musik',         'icon' => '🎵', 'color' => 'text-purple-600',  'bgColor' => 'bg-purple-500',  'description' => 'Album baru, ulasan konser, dan tren musik global & lokal.'],
        'film'          => ['label' => 'Film',          'icon' => '🎬', 'color' => 'text-blue-600',    'bgColor' => 'bg-blue-500',    'description' => 'Review film, festival internasional, dan berita industri perfilman.'],
        'entertainment' => ['label' => 'Entertainment', 'icon' => '✨', 'color' => 'text-orange-600',  'bgColor' => 'bg-orange-500',  'description' => 'Pop culture, streaming, anime, dan semua hal hiburan lainnya.'],
    ];

    public function getCategoryMetaAttribute(): array
    {
        return self::$categoryMeta[$this->category] ?? [];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColor[$this->status] ?? '';
    }

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

    public function submitForReview(): bool
    {
        if (!in_array($this->status, ['draft', 'rejected'])) {
            return false;
        }

        $this->fill([
            'status' => 'pending',
            'submitted_at' => now(),
        ])->save();

        return true;
    }

    public function approve(string $reviewerName, ?string $note = null): bool
    {
        $this->fill([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerName,
            'review_note' => $note,
        ])->save();

        return true;
    }

    public function reject(string $reviewerName, string $reason): bool
    {
        $this->fill([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerName,
            'review_note' => $reason,
        ])->save();

        return true;
    }

    public function publish(): bool
    {
        if (!in_array($this->status, ['approved', 'pending'])) {
            return false;
        }

        $this->fill([
            'slug' => $this->slug ?: self::generateSlug($this->title),
            'status' => 'published',
            'reviewed_at' => now(),
        ])->save();

        return true;
    }

    public function unpublish(): bool
    {
        $this->fill([
            'status' => 'withdrawn',
            'published_article_id' => null,
        ])->save();

        return true;
    }

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }
        return $slug;
    }
}
