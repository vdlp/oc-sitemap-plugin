<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Dto\Definition;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotFound;
use Vdlp\Sitemap\Classes\Exceptions\InvalidGenerator;
use Vdlp\Sitemap\Classes\Exceptions\InvalidPriority;

/**
 * Interface SitemapGenerator
 *
 * @package Vdlp\Sitemap\Classes\Contracts
 */
interface SitemapGenerator
{
    public const GENERATE_EVENT = 'vdlp.sitemap.registerDefinitionGenerator';
    public const INVALIDATE_CACHE_EVENT = 'vdlp.sitemap.invalidateCache';
    public const EXCLUDE_URLS_EVENT = 'vdlp.sitemap.excludeUrls';

    /**
     * @return bool
     */
    public function invalidateCache(): bool;

    /**
     * @return void
     * @throws InvalidGenerator
     */
    public function generate(): void;

    /**
     * @return void
     */
    public function output(): void;

    /**
     * Update definition by url
     *
     * @param Definition $definition
     * @param string|null $oldUrl
     * @return void
     * @throws DtoNotFound
     * @throws InvalidPriority
     */
    public function updateDefinition(Definition $definition, ?string $oldUrl = null): void;

    /**
     * Add new definition
     *
     * @param Definition $definition
     * @return void
     * @throws DtoNotAccepted
     */
    public function addDefinition(Definition $definition): void;

    /**
     * @param Definition $definition
     * @param string|null $oldUrl
     * @return void
     * @throws DtoNotAccepted
     * @throws InvalidPriority
     */
    public function updateOrAddDefinition(Definition $definition, ?string $oldUrl = null): void;

    /**
     * @param string $url
     * @return void
     * @throws DtoNotFound
     */
    public function deleteDefinition(string $url): void;
}
