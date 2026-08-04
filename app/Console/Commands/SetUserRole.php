<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Perintah artisan untuk mengubah role sebuah user.
 *
 * Contoh penggunaan:
 *   php artisan user:set-role rizky redaksi
 *
 * Valid role: reader, pewarta, redaksi.
 */
class SetUserRole extends Command
{
    /** Signature / argumen perintah. */
    protected $signature = 'user:set-role {username} {role=pewarta}';

    /** Deskripsi yang tampil di daftar perintah artisan. */
    protected $description = 'Set role user (pewarta, redaksi, reader)';

    /**
     * Mengeksekusi perintah.
     *
     * @return int Kode keluar 0 (sukses) atau 1 (gagal).
     */
    public function handle(): int
    {
        $username = strtolower($this->argument('username'));
        $role     = strtolower($this->argument('role'));

        if (!in_array($role, ['reader', 'pewarta', 'redaksi'])) {
            $this->error('Role tidak valid. Pilihan: reader, pewarta, redaksi');

            return 1;
        }

        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error("User '{$username}' tidak ditemukan.");

            return 1;
        }

        $oldRole = $user->role;
        $user->update(['role' => $role]);

        $this->info("✓ {$user->display_name} ({$username}): {$oldRole} → {$role}");

        return 0;
    }
}
