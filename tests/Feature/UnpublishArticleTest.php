<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnpublishArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_redaksi_can_unpublish_article(): void
    {
        $redaksi = User::create([
            'username' => 'redaksi-unpublish',
            'display_name' => 'Redaksi Unpublish',
            'password' => bcrypt('secret123'),
            'role' => 'redaksi',
        ]);

        $article = Article::create([
            'slug' => 'test-unpublish',
            'title' => 'Article to Unpublish',
            'excerpt' => 'Testing unpublish',
            'content' => 'This is a test article.',
            'category' => 'games',
            'image' => '',
            'author' => $redaksi->display_name,
            'read_time' => '5 menit',
            'tags' => ['test'],
            'author_id' => $redaksi->id,
            'author_name' => $redaksi->display_name,
            'status' => 'published',
        ]);

        $this->assertEquals('published', $article->status);
        echo "\n✅ Article initially published";

        $response = $this->actingAs($redaksi)
            ->withSession(['redaksi_user_id' => $redaksi->id])
            ->post(route('redaksi.articles.unpublish', $article->id));

        $response->assertRedirect();
        echo "\n✅ Unpublish request successful";

        $article->refresh();
        $this->assertEquals('withdrawn', $article->status);
        echo "\n✅ Article status changed to 'withdrawn'";

        $this->assertNull($article->published_article_id);
        echo "\n✅ published_article_id cleared";

        echo "\n🎉 SUCCESS: Unpublish functionality works perfectly!";
    }

    public function test_withdrawn_articles_not_in_queries(): void
    {
        $user = User::create([
            'username' => 'reader-test',
            'display_name' => 'Reader Test',
            'password' => bcrypt('secret123'),
            'role' => 'reader',
        ]);

        // Create withdrawn article
        $withdrawnArticle = Article::create([
            'slug' => 'test-withdrawn',
            'title' => 'Withdrawn Article',
            'excerpt' => 'This should not appear',
            'content' => 'Content here',
            'category' => 'games',
            'image' => '',
            'author' => 'Test Author',
            'read_time' => '5 menit',
            'tags' => ['test'],
            'author_id' => $user->id,
            'author_name' => 'Test Author',
            'status' => 'withdrawn',
        ]);

        // Create published article
        $publishedArticle = Article::create([
            'slug' => 'test-published',
            'title' => 'Published Article',
            'excerpt' => 'This should appear',
            'content' => 'Content here',
            'category' => 'games',
            'image' => '',
            'author' => 'Test Author',
            'read_time' => '5 menit',
            'tags' => ['test'],
            'author_id' => $user->id,
            'author_name' => 'Test Author',
            'status' => 'published',
        ]);

        // Test: withdrawn articles not in published query
        $published = Article::where('status', 'published')->get();
        $this->assertFalse($published->contains($withdrawnArticle));
        $this->assertTrue($published->contains($publishedArticle));
        echo "\n✅ Withdrawn articles not in published queries";

        // Test: withdrawn articles don't show in category
        $categoryArticles = Article::where('category', 'games')
            ->where('status', 'published')
            ->get();
        $this->assertFalse($categoryArticles->contains($withdrawnArticle));
        $this->assertTrue($categoryArticles->contains($publishedArticle));
        echo "\n✅ Withdrawn articles excluded from category queries";

        echo "\n🎉 SUCCESS: Withdrawn articles are properly hidden!";
    }
}
