<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedaksiDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_articles_show_unpublish_action_in_redaksi_dashboard(): void
    {
        $user = User::create([
            'username' => 'redaksi-user',
            'display_name' => 'Redaksi User',
            'password' => bcrypt('secret123'),
            'role' => 'redaksi',
        ]);

        Article::create([
            'slug' => 'berita-tayang',
            'title' => 'Berita Tayang',
            'excerpt' => 'Ringkasan',
            'content' => 'Isi berita',
            'category' => 'games',
            'image' => '',
            'author' => 'Penulis',
            'read_time' => '5 menit',
            'tags' => ['news'],
            'author_id' => $user->id,
            'author_name' => $user->display_name,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['redaksi_user_id' => $user->id])
            ->get(route('redaksi.dashboard'));

        $response->assertOk();
        $response->assertSee('Tarik Artikel', false);
    }
}
