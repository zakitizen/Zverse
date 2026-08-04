<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User — merepresentasikan pengguna aplikasi (pembaca, pewarta, redaksi).
 *
 * Role yang tersedia:
 *  - `reader`  : pembaca (akun dari halaman Daftar).
 *  - `pewarta` : reporter penulis artikel.
 *  - `redaksi` : editor yang meninjau & menerbitkan artikel.
 *
 * Auth memakai Laravel `Authenticatable` + `password` ber-hash otomatis
 * (cast `hashed`).
 */
class User extends Authenticatable
{
    use Notifiable;

    /** Kolom yang boleh diisi massal (mass assignment). */
    protected $fillable = [
        'name', 'username', 'display_name', 'email', 'password', 'avatar_color', 'role',
    ];

    /** Kolom yang disembunyikan saat model di-serialize (JSON). */
    protected $hidden = ['password', 'remember_token'];

    /** Konversi tipe otomatis; password otomatis di-hash saat diisi. */
    protected $casts = ['password' => 'hashed'];

    /** Daftar warna gradien yang tersedia untuk avatar inisial. */
    public static array $avatarColors = [
        'from-orange-500 to-amber-400',
        'from-purple-500 to-violet-400',
        'from-blue-500 to-sky-400',
        'from-green-500 to-emerald-400',
        'from-pink-500 to-fuchsia-400',
        'from-teal-500 to-cyan-400',
        'from-indigo-500 to-blue-400',
        'from-rose-500 to-orange-400',
    ];

    /**
     * Memilih warna avatar secara deterministik dari username.
     *
     * Menjumlahkan nilai ordinal setiap karakter username, lalu memetakan
     * hasilnya ke salah satu warna di `$avatarColors`. Username yang sama
     * selalu menghasilkan warna yang sama.
     *
     * @param string $username Username untuk menentukan warna.
     *
     * @return string Kelas gradien Tailwind (mis. "from-purple-500 to-violet-400").
     */
    public static function pickColor(string $username): string
    {
        $hash = array_sum(array_map('ord', str_split($username)));

        return self::$avatarColors[$hash % count(self::$avatarColors)];
    }

    /**
     * Accessor: inisial nama untuk avatar.
     *
     * Mengambil huruf pertama dari maksimal 2 kata pertama di `display_name`.
     * Contoh: "Rizky Pratama" → "RP".
     *
     * @return string Inisial dalam huruf besar.
     */
    public function getInitialsAttribute(): string
    {
        $initials = collect(explode(' ', (string) $this->display_name))
            ->map(fn ($word) => mb_substr($word, 0, 1))
            ->filter()
            ->take(2)
            ->implode('');

        return strtoupper($initials);
    }

    /**
     * Relasi: semua artikel yang ditulis user ini.
     *
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Relasi: semua komentar milik user ini.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
