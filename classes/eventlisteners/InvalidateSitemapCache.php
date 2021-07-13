<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\EventListeners;

use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator;

final class InvalidateSitemapCache
{
    private SitemapGenerator $sitemapGenerator;

    public function __construct(SitemapGenerator $sitemapGenerator)
    {
        $this->sitemapGenerator = $sitemapGenerator;
    }

    public function handle(): void
    {
        $this->sitemapGenerator->invalidateCache();
    }
}
