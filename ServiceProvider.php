<?php

declare(strict_types=1);

namespace Vdlp\Sitemap;

use Illuminate\Support\ServiceProvider as ServiceProviderBase;
use Vdlp\Sitemap\Classes\Contracts;
use Vdlp\Sitemap\Classes\SitemapGenerator;

final class ServiceProvider extends ServiceProviderBase
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('sitemap.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->app->alias(SitemapGenerator::class, Contracts\SitemapGenerator::class);
    }
}
