<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->migrateSqliteArticles();
            $this->migrateSqliteComments();
            return;
        }

        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('comments')->nullOnDelete();
            }

            if (!Schema::hasColumn('comments', 'reply_to_user_id')) {
                $table->foreignId('reply_to_user_id')->nullable()->after('parent_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('comments', 'content')) {
                $table->text('content')->nullable()->after('body');
            }

            if (!Schema::hasColumn('comments', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (Schema::hasColumn('comments', 'body')) {
            DB::table('comments')->whereNull('content')->orWhere('content', '')->update([
                'content' => DB::raw('body'),
            ]);
        }

        if (Schema::hasColumn('comments', 'likes')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('likes');
            });
        }

        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'likes')) {
                $table->dropColumn('likes');
            }
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            if (Schema::hasTable('comments_old')) {
                DB::statement('ALTER TABLE comments RENAME TO comments_new');
                DB::statement('ALTER TABLE comments_new RENAME TO comments');
                DB::statement('DROP TABLE comments_old');
            }

            DB::statement('PRAGMA foreign_keys=ON');
            return;
        }

        if (!Schema::hasColumn('comments', 'likes')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->integer('likes')->default(0)->after('body');
            });
        }

        if (!Schema::hasColumn('articles', 'likes')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->integer('likes')->default(0)->after('source');
            });
        }

        if (Schema::hasColumn('comments', 'reply_to_user_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('reply_to_user_id');
                $table->dropColumn('reply_to_user_id');
            });
        }

        if (Schema::hasColumn('comments', 'content')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('content');
            });
        }

        if (Schema::hasColumn('comments', 'deleted_at')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('comments', 'parent_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('parent_id');
                $table->dropColumn('parent_id');
            });
        }
    }

    private function migrateSqliteComments(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        if (Schema::hasTable('comments_old')) {
            DB::statement('DROP TABLE comments_old');
        }

        if (Schema::hasTable('comments')) {
            DB::statement('ALTER TABLE comments RENAME TO comments_old');
        }

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->foreignId('reply_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->string('avatar_color')->default('from-orange-500 to-amber-400');
            $table->text('content')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::hasTable('comments_old')) {
            $rows = DB::table('comments_old')->get();
            foreach ($rows as $row) {
                $content = $row->content ?? $row->body ?? '';
                DB::table('comments')->insert([
                    'id' => $row->id,
                    'article_id' => $row->article_id,
                    'user_id' => $row->user_id,
                    'parent_id' => isset($row->parent_id) ? (int) $row->parent_id : null,
                    'reply_to_user_id' => isset($row->reply_to_user_id) ? (int) $row->reply_to_user_id : null,
                    'author_name' => $row->author_name,
                    'avatar_color' => $row->avatar_color,
                    'content' => $content,
                    'body' => $content,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'deleted_at' => $row->deleted_at ?? null,
                ]);
            }

            DB::statement('DROP TABLE comments_old');
        }

        DB::statement('PRAGMA foreign_keys=ON');
    }

    private function migrateSqliteArticles(): void
    {
        if (Schema::hasTable('articles_old')) {
            DB::statement('DROP TABLE articles_old');
        }

        if (Schema::hasTable('articles')) {
            DB::statement('ALTER TABLE articles RENAME TO articles_old');
        }

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('category');
            $table->string('image')->nullable();
            $table->string('author');
            $table->string('read_time')->default('5 menit');
            $table->decimal('rating', 3, 1)->nullable();
            $table->boolean('featured')->default(false);
            $table->json('tags')->nullable();
            $table->string('source')->default('seed');
            $table->string('status')->default('draft');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->text('review_note')->nullable();
            $table->foreignId('published_article_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('articles_old')) {
            $rows = DB::table('articles_old')->get();
            foreach ($rows as $row) {
                DB::table('articles')->insert([
                    'id' => $row->id,
                    'slug' => $row->slug,
                    'title' => $row->title,
                    'excerpt' => $row->excerpt,
                    'content' => $row->content,
                    'category' => $row->category,
                    'image' => $row->image,
                    'author' => $row->author,
                    'read_time' => $row->read_time,
                    'rating' => $row->rating,
                    'featured' => (bool) ($row->featured ?? 0),
                    'tags' => $row->tags,
                    'source' => $row->source,
                    'status' => $row->status,
                    'author_id' => $row->author_id ?? null,
                    'author_name' => $row->author_name,
                    'submitted_at' => $row->submitted_at,
                    'reviewed_at' => $row->reviewed_at,
                    'reviewed_by' => $row->reviewed_by,
                    'review_note' => $row->review_note,
                    'published_article_id' => $row->published_article_id ?? null,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            DB::statement('DROP TABLE articles_old');
        }
    }
};
