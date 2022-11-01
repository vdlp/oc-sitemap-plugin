<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Dto;

final class SitemapConfig
{
    public function __construct(
        private string $cacheKeySitemap,
        private string $cacheKeyDefinitions,
        private string $cacheFilePath,
        private int $cacheTime,
        private bool $cacheForever
    ) {
    }

    public function getCacheKeySitemap(): string
    {
        return $this->cacheKeySitemap;
    }

    public function getCacheKeyDefinitions(): string
    {
        return $this->cacheKeyDefinitions;
    }

    public function getCacheFilePath(): string
    {
        return $this->cacheFilePath;
    }

    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }

    public function isCacheForever(): bool
    {
        return $this->cacheForever;
    }
}
