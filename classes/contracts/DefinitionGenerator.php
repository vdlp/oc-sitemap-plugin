<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Dto\Definitions;

/**
 * Interface SitemapGenerator
 *
 * @package Vdlp\Sitemap\Classes\Contracts
 */
interface DefinitionGenerator
{
    /**
     * @return Definitions
     */
    public function getDefinitions(): Definitions;
}
