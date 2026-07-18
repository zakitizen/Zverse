<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->ensurePublicStorageLink();
    }

    protected function ensurePublicStorageLink(): void
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (file_exists($link) || is_link($link)) {
            return;
        }

        if (!is_dir($target)) {
            return;
        }

        try {
            symlink($target, $link);
        } catch (\Throwable $e) {
            // Ignore if the environment does not allow symlink creation.
        }
    }
}
