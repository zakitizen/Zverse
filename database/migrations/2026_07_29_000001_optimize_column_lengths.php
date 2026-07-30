<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $this->handleSqlite();
            return;
        }

        // ─── USERS ───────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->change();
            $table->string('display_name', 100)->nullable()->change();
            $table->string('avatar_color', 60)->default('from-orange-500 to-amber-400')->change();
            $table->string('email', 100)->nullable()->change();
            $table->string('role', 15)->default('reader')->change();
        });

        // ─── ARTICLES ────────────────────────────────────────────────────────
        Schema::table('articles', function (Blueprint $table) {
            $table->text('title')->change();
            $table->string('slug', 500)->change();
            $table->string('read_time', 20)->default('5 menit')->change();
            $table->string('category', 20)->change();
            $table->string('image', 1000)->nullable()->change();
            $table->string('author', 100)->change();
            $table->string('author_name', 100)->nullable()->change();
            $table->string('reviewed_by', 100)->nullable()->change();
            $table->string('source', 10)->default('seed')->change();
            $table->string('status', 15)->default('draft')->change();
        });

        // ─── COMMENTS ────────────────────────────────────────────────────────
        Schema::table('comments', function (Blueprint $table) {
            $table->string('author_name', 100)->nullable()->change();
            $table->string('avatar_color', 60)->default('from-orange-500 to-amber-400')->change();
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 255)->nullable()->change();
            $table->string('display_name', 255)->nullable()->change();
            $table->string('avatar_color', 255)->default('from-orange-500 to-amber-400')->change();
            $table->string('email', 255)->nullable()->change();
            $table->string('role', 255)->default('reader')->change();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('title', 255)->change();
            $table->string('slug', 255)->change();
            $table->string('read_time', 255)->default('5 menit')->change();
            $table->string('category', 255)->change();
            $table->string('image', 255)->nullable()->change();
            $table->string('author', 255)->change();
            $table->string('author_name', 255)->nullable()->change();
            $table->string('reviewed_by', 255)->nullable()->change();
            $table->string('source', 255)->default('seed')->change();
            $table->string('status', 255)->default('draft')->change();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->string('author_name', 255)->nullable()->change();
            $table->string('avatar_color', 255)->default('from-orange-500 to-amber-400')->change();
        });
    }

    private function handleSqlite(): void
    {
        $checks = [
            ['table' => 'users', 'column' => 'username',      'max' => 50],
            ['table' => 'users', 'column' => 'display_name',   'max' => 100],
            ['table' => 'users', 'column' => 'avatar_color',   'max' => 60],
            ['table' => 'users', 'column' => 'email',          'max' => 100],
            ['table' => 'users', 'column' => 'role',           'max' => 15],
            ['table' => 'articles', 'column' => 'read_time',   'max' => 20],
            ['table' => 'articles', 'column' => 'category',    'max' => 20],
            ['table' => 'articles', 'column' => 'author',      'max' => 100],
            ['table' => 'articles', 'column' => 'author_name', 'max' => 100],
            ['table' => 'articles', 'column' => 'reviewed_by', 'max' => 100],
            ['table' => 'articles', 'column' => 'source',      'max' => 10],
            ['table' => 'articles', 'column' => 'status',      'max' => 15],
            ['table' => 'comments', 'column' => 'author_name', 'max' => 100],
            ['table' => 'comments', 'column' => 'avatar_color','max' => 60],
        ];

        foreach ($checks as $check) {
            $violations = DB::table($check['table'])
                ->where(DB::raw("LENGTH({$check['column']})"), '>', $check['max'])
                ->count();

            if ($violations > 0) {
                echo "WARN: {$check['table']}.{$check['column']} has {$violations} row(s) exceeding {$check['max']} chars. Skipping.\n";
                return;
            }
        }

        echo "SQLite: All data safe. Run migration on MySQL for actual changes.\n";
    }
};
