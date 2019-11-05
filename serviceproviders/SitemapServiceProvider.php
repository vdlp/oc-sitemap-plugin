<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Vdlp\Sitemap\Classes\Contracts;
use Vdlp\Sitemap\Classes\SitemapGenerator;

/**
 * Class SitemapServiceProvider
 *
 * @package Vdlp\Sitemap\ServiceProviders
 */
final class SitemapServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->alias(SitemapGenerator::class, Contracts\SitemapGenerator::class);
    }
}
