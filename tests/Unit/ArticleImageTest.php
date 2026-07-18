<?php

namespace Tests\Unit;

use App\Models\Article;
use Tests\TestCase;

class ArticleImageTest extends TestCase
{
    public function test_image_url_attribute_normalizes_storage_paths(): void
    {
        $article = new Article(['image' => 'storage/articles/sample.jpg']);

        $this->assertSame(asset('storage/articles/sample.jpg'), $article->image_url);
    }
}
