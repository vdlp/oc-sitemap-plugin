<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Dto\Definitions;

interface DefinitionGenerator
{
    public function getDefinitions(): Definitions;
}
