<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Comment — merepresentasikan komentar / balasan pada artikel.
 *
 * Struktur balasan (nested):
 *  - Komentar utama : `parent_id = null`.
 *  - Balasan        : `parent_id` menunjuk ke komentar utama.
 *  - `reply_to_user_id` : user yang sedang dibalas (untuk label @username).
 *
 * Mendukung soft delete (`deleted_at`) sehingga komentar yang dihapus tidak
 * hilang permanen dan pesan bisa ditampilkan sebagai "Komentar dihapus".
 */
class Comment extends Model
{
    use SoftDeletes;

    /** Kolom yang boleh diisi massal (mass assignment). */
    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'reply_to_user_id',
        'author_name',
        'avatar_color',
        'content',
        'body',
    ];

    /** Konversi tipe otomatis. */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi: artikel tempat komentar ini berada.
     *
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Relasi: user pemilik komentar.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: user yang dibalas (untuk balasan/reply).
     *
     * @return BelongsTo
     */
    public function replyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_to_user_id');
    }

    /**
     * Relasi: komentar induk (jika ini balasan).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Relasi: daftar balasan pada komentar ini, diurutkan paling lama dulu.
     *
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest('created_at');
    }
}
