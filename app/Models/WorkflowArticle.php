<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowArticle extends Model
{
    protected $fillable = [
        'title', 'excerpt', 'content', 'category', 'image',
        'read_time', 'rating', 'featured', 'tags',
        'author_id', 'author_name',
        'status', 'submitted_at', 'reviewed_at', 'reviewed_by',
        'review_note', 'published_article_id',
    ];

    protected $casts = [
        'tags'         => 'array',
        'featured'     => 'boolean',
        'rating'       => 'float',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public static array $statusLabel = [
        'draft'     => 'Draft',
        'pending'   => 'Menunggu Review',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        'published' => 'Tayang',
    ];

    public static array $statusColor = [
        'draft'     => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'approved'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
        'published' => 'bg-green-50 text-green-700 border-green-200',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function publishedArticle()
    {
        return $this->belongsTo(Article::class, 'published_article_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColor[$this->status] ?? '';
    }

    public function formatDate(?string $field): string
    {
        if (!$this->$field) return '-';
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $d = $this->$field;
        return sprintf('%02d %s %d, %02d:%02d', $d->day, $months[$d->month - 1], $d->year, $d->hour, $d->minute);
    }
}
