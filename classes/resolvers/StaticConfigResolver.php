<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Resolvers;

use Illuminate\Contracts\Config\Repository;
use Vdlp\Sitemap\Classes\Contracts\ConfigResolver;
use Vdlp\Sitemap\Classes\Dto\SitemapConfig;

final class StaticConfigResolver implements ConfigResolver
{
    public function __construct(private Repository $repository)
    {
    }

    public function getConfig(): SitemapConfig
    {
        return new SitemapConfig(
            'vdlp_sitemap_cache',
            'vdlp_sitemap_definitions',
            'vdlp/sitemap/sitemap.xml',
            (int) $this->repository->get('sitemap.cache_time', 3600),
            (bool) $this->repository->get('sitemap.cache_forever', false)
        );
    }
}
