<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->longText('content');
            $table->enum('category', ['games', 'musik', 'film', 'entertainment']);
            $table->string('image');
            $table->string('author');
            $table->string('read_time')->default('5 menit');
            $table->decimal('rating', 3, 1)->nullable();
            $table->boolean('featured')->default(false);
            $table->json('tags')->nullable();
            $table->enum('source', ['seed', 'admin'])->default('seed');
            $table->integer('likes')->default(0);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->string('avatar_color')->default('from-orange-500 to-amber-400');
            $table->text('body');
            $table->integer('likes')->default(0);
            $table->timestamps();
        });

        Schema::create('shorts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('author');
            $table->string('handle');
            $table->enum('category', ['games', 'musik', 'film', 'entertainment']);
            $table->string('video_url');
            $table->string('thumbnail');
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->string('views')->default('0');
            $table->string('duration')->default('1:00');
            $table->json('tags')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('shorts');
        Schema::dropIfExists('articles');
    }
};
