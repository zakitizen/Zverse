<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PewartaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_form_can_submit_article_to_redaksi(): void
    {
        $user = User::create([
            'username' => 'reporter1',
            'display_name' => 'Reporter Satu',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $this->actingAs($user);
        session(['pewarta_user_id' => $user->id]);

        $response = $this->post(route('pewarta.articles.store'), [
            'title' => 'Judul draft',
            'excerpt' => 'Ringkasan draft',
            'content' => 'Isi draft',
            'category' => 'games',
            'read_time' => '5 menit',
            'tags' => 'news',
            'submit_to_redaksi' => '1',
        ]);

        $response->assertRedirect(route('pewarta.dashboard'));

        $article = Article::first();
        $this->assertNotNull($article);
        $this->assertSame('pending', $article->status);
        $this->assertNotNull($article->submitted_at);
    }

    public function test_rejected_article_can_be_submitted_for_review_again(): void
    {
        $user = User::create([
            'username' => 'reporter2',
            'display_name' => 'Reporter Dua',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $article = Article::create([
            'slug' => 'judul-ditolak',
            'title' => 'Judul ditolak',
            'excerpt' => 'Ringkasan ditolak',
            'content' => 'Isi ditolak',
            'category' => 'games',
            'image' => '',
            'author' => $user->display_name,
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => $user->display_name,
            'status' => 'rejected',
            'review_note' => 'Perlu revisi',
        ]);

        $this->actingAs($user);
        session(['pewarta_user_id' => $user->id]);

        $response = $this->from(route('pewarta.dashboard'))->post(route('pewarta.articles.submit', $article->id));

        $response->assertRedirect(route('pewarta.dashboard'));
        $article->refresh();
        $this->assertSame('pending', $article->status);
        $this->assertNotNull($article->submitted_at);
    }

    public function test_dashboard_submit_form_renders_csrf_token(): void
    {
        $user = User::create([
            'username' => 'reporter3',
            'display_name' => 'Reporter Tiga',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $article = Article::create([
            'slug' => 'judul-draft',
            'title' => 'Judul draft',
            'excerpt' => 'Ringkasan draft',
            'content' => 'Isi draft',
            'category' => 'games',
            'image' => '',
            'author' => $user->display_name,
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => $user->display_name,
            'status' => 'draft',
        ]);

        $html = view('pewarta.dashboard', [
            'user' => $user,
            'articles' => collect([$article]),
        ])->render();

        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString(route('pewarta.articles.submit', $article->id), $html);
    }
}
