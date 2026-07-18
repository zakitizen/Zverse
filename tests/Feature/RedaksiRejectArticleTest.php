<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedaksiRejectArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_redaksi_can_reject_pending_article(): void
    {
        $redaksi = User::create([
            'username' => 'redaksi-reject',
            'display_name' => 'Redaksi Reject',
            'password' => bcrypt('secret123'),
            'role' => 'redaksi',
        ]);

        $article = Article::create([
            'slug' => 'artikel-untuk-ditolak',
            'title' => 'Artikel Untuk Ditolak',
            'excerpt' => 'Ringkasan',
            'content' => 'Isi artikel',
            'category' => 'games',
            'image' => '',
            'author' => 'Penulis',
            'read_time' => '5 menit',
            'tags' => [],
            'status' => 'pending',
        ]);

        $response = $this->actingAs($redaksi)
            ->withSession(['redaksi_user_id' => $redaksi->id])
            ->post(route('redaksi.articles.reject', $article->id), [
                'reason' => 'Konten tidak sesuai standar editorial'
            ]);

        $response->assertSessionHas('success', 'Artikel ditolak.');

        $article->refresh();
        $this->assertSame('rejected', $article->status);
        $this->assertSame('Redaksi Reject', $article->reviewed_by);
        $this->assertSame('Konten tidak sesuai standar editorial', $article->review_note);
    }
}
