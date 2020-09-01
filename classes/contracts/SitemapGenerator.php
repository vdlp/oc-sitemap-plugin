<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Dto\Definition;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotFound;
use Vdlp\Sitemap\Classes\Exceptions\InvalidGenerator;
use Vdlp\Sitemap\Classes\Exceptions\InvalidPriority;

interface SitemapGenerator
{
    public const GENERATE_EVENT = 'vdlp.sitemap.registerDefinitionGenerator';
    public const INVALIDATE_CACHE_EVENT = 'vdlp.sitemap.invalidateCache';
    public const EXCLUDE_URLS_EVENT = 'vdlp.sitemap.excludeUrls';

    public function invalidateCache(): bool;

    /**
     * @throws InvalidGenerator
     */
    public function generate(): void;

    public function output(): void;

    /**
     * @throws DtoNotFound
     * @throws InvalidPriority
     */
    public function updateDefinition(Definition $definition, ?string $oldUrl = null): void;

    /**
     * @throws DtoNotAccepted
     */
    public function addDefinition(Definition $definition): void;

    /**
     * @throws DtoNotAccepted
     * @throws InvalidPriority
     */
    public function updateOrAddDefinition(Definition $definition, ?string $oldUrl = null): void;

    /**
     * @throws DtoNotFound
     */
    public function deleteDefinition(string $url): void;
}
