<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_article_creation_with_image_upload_and_display(): void
    {
        // 1. CREATE PEWARTA USER
        $pewarta = User::create([
            'username' => 'pewarta-test',
            'display_name' => 'Pewarta Tester',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        Storage::fake('public');

        // 2. SIMULATE EDITOR IMAGE UPLOAD
        $file = UploadedFile::fake()->create('test-photo.png', 100, 'image/png');

        $uploadResponse = $this->actingAs($pewarta)
            ->withSession(['pewarta_user_id' => $pewarta->id])
            ->postJson(route('pewarta.articles.upload-image'), ['image' => $file]);

        $uploadResponse->assertOk();
        $uploadedUrl = $uploadResponse->json('url');

        echo "\n✅ IMAGE UPLOAD SUCCESS: " . $uploadedUrl;
        $this->assertStringContainsString('/storage/articles/', $uploadedUrl);

        // 3. CREATE ARTICLE WITH MARKDOWN IMAGE
        $markdown = "Judul test\n\n**Ini bold text**\n\n![Test Photo]($uploadedUrl)\n\n*Ini italic text*";

        $createResponse = $this->actingAs($pewarta)
            ->withSession(['pewarta_user_id' => $pewarta->id])
            ->post(route('pewarta.articles.store'), [
                'title' => 'Test Article with Image',
                'excerpt' => 'Testing image upload',
                'content' => $markdown,
                'category' => 'games',
                'action' => 'submit',
            ]);

        $createResponse->assertRedirect();

        // 4. GET ARTICLE FROM DATABASE
        $article = Article::where('title', 'Test Article with Image')->firstOrFail();
        echo "\n✅ ARTICLE CREATED: ID=" . $article->id;
        echo "\n📝 Markdown Content:\n" . $article->content;

        // 5. VERIFY MARKDOWN IS STORED CORRECTLY
        $this->assertStringContainsString('**Ini bold text**', $article->content);
        $this->assertStringContainsString('*Ini italic text*', $article->content);
        $this->assertStringContainsString("![Test Photo]($uploadedUrl)", $article->content);

        // 6. PUBLISH ARTICLE (Redaksi approval)
        $redaksi = User::create([
            'username' => 'redaksi-test',
            'display_name' => 'Redaksi Tester',
            'password' => bcrypt('secret123'),
            'role' => 'redaksi',
        ]);

        $approveResponse = $this->actingAs($redaksi)
            ->withSession(['redaksi_user_id' => $redaksi->id])
            ->post(route('redaksi.articles.approve', $article->id));

        $approveResponse->assertRedirect();

        $article->refresh();
        $this->assertEquals('approved', $article->status);
        echo "\n✅ ARTICLE APPROVED";

        // 7. DISPLAY ARTICLE AND VERIFY MARKDOWN RENDERS
        $displayResponse = $this->get(route('article.show', $article->id));

        $displayResponse->assertOk();
        echo "\n✅ ARTICLE PAGE LOADS SUCCESSFULLY";

        // 8. VERIFY HTML CONTAINS RENDERED MARKDOWN ELEMENTS
        $html = $displayResponse->content();

        // Check bold rendering
        $this->assertStringContainsString('<strong', $html);
        echo "\n✅ BOLD TEXT RENDERED: <strong> tag found";

        // Check italic rendering  
        $this->assertStringContainsString('<em>', $html);
        echo "\n✅ ITALIC TEXT RENDERED: <em> tag found";

        // Check image rendering - MOST IMPORTANT
        $this->assertStringContainsString('<img', $html);
        echo "\n✅ IMAGE RENDERED: <img> tag found";

        $this->assertStringContainsString('src="' . $uploadedUrl . '"', $html);
        echo "\n✅ IMAGE URL CORRECT: src attribute matches uploaded URL";

        $this->assertStringContainsString('alt="Test Photo"', $html);
        echo "\n✅ IMAGE ALT TEXT CORRECT";

        // 9. VERIFY NO RAW MARKDOWN SYNTAX IN OUTPUT
        $this->assertStringNotContainsString('![Test Photo]', $displayResponse->content());
        echo "\n✅ NO RAW MARKDOWN: Image markdown syntax not in HTML output";

        echo "\n\n🎉 SUCCESS: Complete image upload → article creation → publication → rendering workflow works perfectly!\n";
    }
}
