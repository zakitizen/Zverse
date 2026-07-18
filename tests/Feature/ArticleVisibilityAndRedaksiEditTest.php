<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ArticleVisibilityAndRedaksiEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_only_shows_published_articles(): void
    {
        $user = User::create([
            'username' => 'reporter-home',
            'display_name' => 'Reporter Home',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        Article::create([
            'slug' => 'artikel-tayang',
            'title' => 'Artikel Tayang',
            'excerpt' => 'Sudah disetujui',
            'content' => 'Isi publik',
            'category' => 'games',
            'image' => '',
            'author' => $user->display_name,
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => $user->display_name,
            'status' => 'published',
        ]);

        Article::create([
            'slug' => 'artikel-pending',
            'title' => 'Artikel Pending',
            'excerpt' => 'Belum disetujui',
            'content' => 'Isi draft',
            'category' => 'games',
            'image' => '',
            'author' => $user->display_name,
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => $user->display_name,
            'status' => 'pending',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Artikel Tayang');
        $response->assertDontSee('Artikel Pending');
    }

    public function test_uploaded_images_are_available_from_public_storage(): void
    {
        $user = User::create([
            'username' => 'reporter-upload',
            'display_name' => 'Reporter Upload',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $this->actingAs($user);
        session(['pewarta_user_id' => $user->id]);

        $response = $this->post(route('pewarta.articles.store'), [
            'title' => 'Artikel dengan upload',
            'excerpt' => 'Ringkasan artikel',
            'content' => 'Isi artikel',
            'category' => 'games',
            'image' => '',
            'read_time' => '5 menit',
            'tags' => '',
            'image_upload' => UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('pewarta.dashboard'));

        $article = Article::latest()->first();
        $this->assertNotNull($article);
        $this->assertNotEmpty($article->image);

        $path = parse_url($article->image, PHP_URL_PATH);
        $this->assertNotEmpty($path);
        $this->assertTrue(file_exists(public_path(ltrim($path, '/'))), 'Uploaded image should be available via public storage.');
    }

    public function test_redaksi_can_open_edit_form_for_submitted_article(): void
    {
        $user = User::create([
            'username' => 'redaksi1',
            'display_name' => 'Redaksi Satu',
            'password' => bcrypt('secret123'),
            'role' => 'redaksi',
        ]);

        $writer = User::create([
            'username' => 'writer-edit',
            'display_name' => 'Writer Edit',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $article = Article::create([
            'slug' => 'draft-dari-pewarta',
            'title' => 'Draft Pewarta',
            'excerpt' => 'Ringkasan draft',
            'content' => 'Isi draft',
            'category' => 'games',
            'image' => '',
            'author' => $writer->display_name,
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $writer->id,
            'author_name' => $writer->display_name,
            'status' => 'pending',
        ]);

        $this->actingAs($user);
        session(['redaksi_user_id' => $user->id]);

        $response = $this->get(route('redaksi.articles.edit', $article->id));

        $response->assertOk();
        $response->assertSee('Edit Artikel');
    }
}
