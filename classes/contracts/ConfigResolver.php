<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Dto\SitemapConfig;

interface ConfigResolver
{
    public function getConfig(): SitemapConfig;
}
