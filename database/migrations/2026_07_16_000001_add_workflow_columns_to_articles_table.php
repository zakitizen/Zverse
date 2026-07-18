<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'author_id')) {
                $table->foreignId('author_id')->nullable()->after('author')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('articles', 'author_name')) {
                $table->string('author_name')->nullable()->after('author_id');
            }
            if (!Schema::hasColumn('articles', 'status')) {
                $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'published'])->default('draft')->after('author_name');
            }
            if (!Schema::hasColumn('articles', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('articles', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('articles', 'reviewed_by')) {
                $table->string('reviewed_by')->nullable()->after('reviewed_at');
            }
            if (!Schema::hasColumn('articles', 'review_note')) {
                $table->text('review_note')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('articles', 'published_article_id')) {
                $table->foreignId('published_article_id')->nullable()->after('review_note')->constrained('articles')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'published_article_id')) {
                $table->dropConstrainedForeignId('published_article_id');
            }
            if (Schema::hasColumn('articles', 'author_id')) {
                $table->dropConstrainedForeignId('author_id');
            }
            if (Schema::hasColumn('articles', 'author_name')) {
                $table->dropColumn('author_name');
            }
            if (Schema::hasColumn('articles', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('articles', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
            if (Schema::hasColumn('articles', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('articles', 'reviewed_by')) {
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('articles', 'review_note')) {
                $table->dropColumn('review_note');
            }
        });
    }
};
