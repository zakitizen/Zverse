<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'display_name', 'email', 'password', 'avatar_color', 'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

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

    public static function pickColor(string $username): string
    {
        $hash = array_sum(array_map('ord', str_split($username)));
        return self::$avatarColors[$hash % count(self::$avatarColors)];
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->display_name);
        return strtoupper(substr(implode('', array_column(array_map(fn($w) => ['i' => $w[0] ?? ''], $words), 'i')), 0, 2));
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }
}
