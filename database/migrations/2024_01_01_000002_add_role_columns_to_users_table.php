<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'display_name')) {
                $table->string('display_name')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'avatar_color')) {
                $table->string('avatar_color')->default('from-orange-500 to-amber-400')->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['reader', 'pewarta', 'redaksi', 'admin'])->default('reader')->after('avatar_color');
            }

            // Jadikan email & name nullable
            $table->string('email')->nullable()->change();
            $table->string('name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['username', 'display_name', 'avatar_color', 'role'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('users', $c));
            if ($existing) $table->dropColumn(array_values($existing));

            $table->string('email')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
        });
    }
};
