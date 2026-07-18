<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we just add CHECK constraint to allow new value
        // SQLite doesn't have native ENUM, it uses CHECK constraints
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: Drop and recreate with new CHECK constraint
            // First, check if table already has the constraint pattern
            DB::statement("
                CREATE TABLE IF NOT EXISTS articles_backup AS SELECT * FROM articles;
            ");

            // This is tricky with SQLite. Let's just update the constraint if needed
            // Actually, since SQLite is used for testing, let's use a pragma approach
            DB::statement("
                PRAGMA foreign_keys=OFF;
            ");

            // Rename old table
            DB::statement("ALTER TABLE articles RENAME TO articles_old;");

            // Create new table with updated CHECK constraint
            DB::statement("
                CREATE TABLE articles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    slug TEXT NOT NULL UNIQUE,
                    title TEXT NOT NULL,
                    excerpt TEXT,
                    content TEXT,
                    category TEXT,
                    image TEXT,
                    author TEXT,
                    read_time TEXT DEFAULT '5 menit',
                    tags JSON,
                    rating DECIMAL(2,1),
                    featured INTEGER DEFAULT 0,
                    source TEXT DEFAULT 'seed',
                    likes INTEGER DEFAULT 0,
                    author_id INTEGER,
                    author_name TEXT,
                    status TEXT DEFAULT 'draft' CHECK(status IN ('draft', 'pending', 'approved', 'rejected', 'published', 'withdrawn')),
                    submitted_at TIMESTAMP NULL,
                    reviewed_at TIMESTAMP NULL,
                    reviewed_by TEXT,
                    review_note TEXT,
                    published_article_id INTEGER,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY(author_id) REFERENCES users(id),
                    FOREIGN KEY(published_article_id) REFERENCES articles(id)
                )
            ");

            // Copy data
            DB::statement("
                INSERT INTO articles 
                SELECT id, slug, title, excerpt, content, category, image, author, read_time, tags, 
                       rating, featured, source, likes, author_id, author_name, status, submitted_at, 
                       reviewed_at, reviewed_by, review_note, published_article_id, created_at, updated_at 
                FROM articles_old
            ");

            // Drop old table
            DB::statement("DROP TABLE articles_old;");

            DB::statement("PRAGMA foreign_keys=ON;");
        } else {
            // For MySQL/MariaDB: update ENUM
            Schema::table('articles', function (Blueprint $table) {
                DB::statement("
                    ALTER TABLE articles MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'published', 'withdrawn') DEFAULT 'draft'
                ");
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("PRAGMA foreign_keys=OFF;");

            DB::statement("ALTER TABLE articles RENAME TO articles_old;");

            DB::statement("
                CREATE TABLE articles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    slug TEXT NOT NULL UNIQUE,
                    title TEXT NOT NULL,
                    excerpt TEXT,
                    content TEXT,
                    category TEXT,
                    image TEXT,
                    author TEXT,
                    read_time TEXT DEFAULT '5 menit',
                    tags JSON,
                    rating DECIMAL(2,1),
                    featured INTEGER DEFAULT 0,
                    source TEXT DEFAULT 'seed',
                    likes INTEGER DEFAULT 0,
                    author_id INTEGER,
                    author_name TEXT,
                    status TEXT DEFAULT 'draft' CHECK(status IN ('draft', 'pending', 'approved', 'rejected', 'published')),
                    submitted_at TIMESTAMP NULL,
                    reviewed_at TIMESTAMP NULL,
                    reviewed_by TEXT,
                    review_note TEXT,
                    published_article_id INTEGER,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY(author_id) REFERENCES users(id),
                    FOREIGN KEY(published_article_id) REFERENCES articles(id)
                )
            ");

            DB::statement("
                INSERT INTO articles 
                SELECT id, slug, title, excerpt, content, category, image, author, read_time, tags, 
                       rating, featured, source, likes, author_id, author_name, 
                       CASE WHEN status = 'withdrawn' THEN 'published' ELSE status END, 
                       submitted_at, reviewed_at, reviewed_by, review_note, published_article_id, created_at, updated_at 
                FROM articles_old
            ");

            DB::statement("DROP TABLE articles_old;");

            DB::statement("PRAGMA foreign_keys=ON;");
        } else {
            Schema::table('articles', function (Blueprint $table) {
                DB::statement("
                    ALTER TABLE articles MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'published') DEFAULT 'draft'
                ");
            });
        }
    }
};
