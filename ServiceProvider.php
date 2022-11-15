<?php

declare(strict_types=1);

namespace Vdlp\Sitemap;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
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
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'sitemap');

        $this->app->alias(SitemapGenerator::class, Contracts\SitemapGenerator::class);

        $this->app->bind(Contracts\ConfigResolver::class, static function (Application $app): Contracts\ConfigResolver {
            /** @var Repository $config */
            $config = $app->make(Repository::class);

            return $app->make($config->get('sitemap.config_resolver'));
        });
    }
}
