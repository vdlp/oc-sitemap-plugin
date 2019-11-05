<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\EventSubscribers;

use October\Rain\Events\Dispatcher;
use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator;
use Vdlp\Sitemap\Classes\EventListeners\InvalidateSitemapCache;

/**
 * Class SitemapSubscriber
 *
 * @package Vdlp\Sitemap\Classes\EventSubscribers
 */
final class SitemapSubscriber implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function subscribe(Dispatcher $dispatcher): void
    {
        $dispatcher->listen(
            SitemapGenerator::INVALIDATE_CACHE_EVENT,
            InvalidateSitemapCache::class
        );
    }
}
