<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\EventListeners;

use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator;

final class InvalidateSitemapCache
{
    public function handle(): void
    {
        /** @var SitemapGenerator $generator */
        $generator = resolve(SitemapGenerator::class);
        $generator->invalidateCache();
    }
}
