<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleMarkdownRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pewarta_can_upload_image_from_editor(): void
    {
        $user = User::create([
            'username' => 'upload-user',
            'display_name' => 'Upload User',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('editor-photo.png', 100, 'image/png');

        $response = $this->actingAs($user)
            ->withSession(['pewarta_user_id' => $user->id])
            ->postJson(route('pewarta.articles.upload-image'), ['image' => $file]);

        $response->assertOk();
        $response->assertJsonStructure(['url']);
        $this->assertStringContainsString('/storage/articles/', $response->json('url'));
        $this->assertNotEmpty(Storage::disk('public')->allFiles('articles'));
    }

    public function test_article_detail_renders_bold_italic_and_images(): void
    {
        $user = User::create([
            'username' => 'markdown-user',
            'display_name' => 'Markdown User',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $article = Article::create([
            'slug' => 'markdown-artikel',
            'title' => 'Artikel Markdown',
            'excerpt' => 'Ringkasan',
            'content' => "**Bold text**\n*Italic text*\n![Alt](https://example.com/image.jpg)",
            'category' => 'games',
            'image' => '',
            'author' => $user->display_name,
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => $user->display_name,
            'status' => 'published',
        ]);

        $response = $this->get(route('article.show', $article->id));

        $response->assertOk();
        $response->assertSee('Bold text', false);
        $response->assertSee('Italic text', false);
        $response->assertSee('https://example.com/image.jpg', false);
        $response->assertSee('<img', false);
    }

    public function test_authenticated_user_can_comment_and_reply_to_existing_comment(): void
    {
        $user = User::create([
            'username' => 'commenter',
            'display_name' => 'Commenter User',
            'password' => bcrypt('secret123'),
            'role' => 'reader',
        ]);

        $article = Article::create([
            'slug' => 'comment-artikel',
            'title' => 'Artikel Komentar',
            'excerpt' => 'Ringkasan',
            'content' => 'Isi artikel',
            'category' => 'games',
            'image' => '',
            'author' => 'Author',
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => 'Author',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->post(route('article.comment', $article->id), ['body' => 'Komentar awal']);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'author_name' => 'Commenter User',
            'body' => 'Komentar awal',
            'parent_id' => null,
        ]);

        $comment = $article->comments()->first();

        $replyResponse = $this->actingAs($user)
            ->post(route('article.comment', $article->id), [
                'body' => 'Balasan komentar',
                'parent_id' => $comment->id,
            ]);

        $replyResponse->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'author_name' => 'Commenter User',
            'body' => 'Balasan komentar',
            'parent_id' => $comment->id,
        ]);

        $viewResponse = $this->get(route('article.show', $article->id));

        $viewResponse->assertOk();
        $viewResponse->assertSee('Commenter User', false);
        $viewResponse->assertSee('Balasan komentar', false);
    }
}
