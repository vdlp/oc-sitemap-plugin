<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use RuntimeException;
use Vdlp\Sitemap\Classes\Contracts;
use Vdlp\Sitemap\Classes\Dto;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotFound;
use Vdlp\Sitemap\Classes\Exceptions\InvalidGenerator;

/**
 * Class SitemapGenerator
 *
 * @package Vdlp\Sitemap\Classes
 */
final class SitemapGenerator implements Contracts\SitemapGenerator
{
    private const CACHE_KEY_SITEMAP = 'vdlp_sitemap_cache';
    private const CACHE_DEFINITIONS = 'vdlp_sitemap_definitions';
    private const VDLP_SITEMAP_PATH = 'vdlp/sitemap/sitemap.xml';

    /**
     * @var Repository
     */
    private $cache;

    /**
     * @var Dispatcher
     */
    private $event;

    /**
     * @var integer
     */
    private $cacheTime;

    /**
     * @var bool
     */
    private $cacheForever;

    /**
     * @param Repository $cache
     * @param Dispatcher $event
     */
    public function __construct(Repository $cache, Dispatcher $event)
    {
        $this->cache = $cache;
        $this->event = $event;
        $this->cacheTime = config('vdlp.sitemap::sitemap_cache_time', 60);
        $this->cacheForever = config('vdlp.sitemap::sitemap_cache_forever', false);
    }

    /**
     * {@inheritDoc}
     */
    public function invalidateCache(): bool
    {
        return $this->invalidateSitemapCache() && $this->invalidateDefinitionsCache();
    }

    /**
     * {@inheritDoc}
     * @throws RuntimeException
     */
    public function generate(): void
    {
        $fromCache = $this->cache->has(self::CACHE_KEY_SITEMAP);

        $path = storage_path(self::VDLP_SITEMAP_PATH);

        if (!$fromCache || !file_exists($path)) {
            $this->createXmlFile($this->rememberDefinitionsFromCache(), $path);
            $this->updateCache(self::CACHE_KEY_SITEMAP, true);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function output(): void
    {
        header('Content-Type: application/xml');

        $handle = fopen(storage_path(self::VDLP_SITEMAP_PATH), 'rb');

        fpassthru($handle);

        fclose($handle);

        exit;
    }

    /**
     * {@inheritDoc}
     */
    public function updateDefinition(Dto\Definition $definition, ?string $oldUrl = null): void
    {
        $definitions = $this->rememberDefinitionsFromCache();

        $found = false;

        $urlToCheck = $oldUrl ?? $definition->getUrl();

        /** @var Dto\Definition $cachedDefinition */
        foreach ($definitions->getItems() as $cachedDefinition) {
            if ($cachedDefinition->getUrl() === $urlToCheck) {
                $cachedDefinition->setUrl($definition->getUrl())
                    ->setChangeFrequency($definition->getChangeFrequency())
                    ->setPriority($definition->getPriority())
                    ->setModifiedAt($definition->getModifiedAt());

                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new DtoNotFound();
        }

        $this->updateCache(self::CACHE_DEFINITIONS, $definitions);
        $this->invalidateSitemapCache();
    }

    /**
     * {@inheritDoc}
     */
    public function addDefinition(Dto\Definition $definition): void
    {
        if (!$this->allowAdd($this->getExcludeUrls(), $definition)) {
            return;
        }

        $definitions = $this->rememberDefinitionsFromCache();
        $definitions->addItem($definition);
        $this->updateCache(self::CACHE_DEFINITIONS, $definitions);
        $this->invalidateSitemapCache();
    }

    /**
     * {@inheritDoc}
     */
    public function updateOrAddDefinition(Dto\Definition $definition, ?string $oldUrl = null): void
    {
        try {
            $this->updateDefinition($definition, $oldUrl);
        } catch (DtoNotFound $e) {
            $this->addDefinition($definition);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDefinition(string $url): void
    {
        $definitions = $this->rememberDefinitionsFromCache();
        $definitions->removeDefinitionByUrl($url);
        $this->updateCache(self::CACHE_DEFINITIONS, $definitions);
        $this->invalidateSitemapCache();
    }

    /**
     * @param Dto\Definitions $definitions
     * @param string $path
     * @return void
     * @throws RuntimeException
     */
    private function createXmlFile(Dto\Definitions $definitions, string $path): void
    {
        if (!file_exists(dirname($path))
            && !mkdir($concurrentDirectory = dirname($path), 0777, true)
            && !is_dir($concurrentDirectory)
        ) {
            throw new RuntimeException(sprintf(
                'Vdlp.Sitemap: Directory "%s" was not created.',
                $concurrentDirectory
            ));
        }

        @unlink($path);
        @touch($path);

        $file = fopen($path, 'a+b');

        fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>');
        fwrite($file, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        /** @var Dto\Definition $definition */
        foreach ($definitions->getItems() as $definition) {
            $xml = '<url>';
            $xml .= '<loc>' . $definition->getUrl() .'</loc>';
            $xml .= '<lastmod>' . $definition->getModifiedAt() . '</lastmod>';
            $xml .= '<priority>' . $definition->getPriorityFloat() . '</priority>';
            $xml .= '<changefreq>' . $definition->getChangeFrequency() . '</changefreq>';
            $xml .= '</url>';

            fwrite($file, $xml);
        }

        fwrite($file, '</urlset>');
        fclose($file);
    }

    /**
     * @return Dto\Definitions
     * @throws InvalidGenerator
     * @throws Exceptions\DtoNotAccepted
     */
    private function getDefinitions(): Dto\Definitions
    {
        $definitions = new Dto\Definitions();

        $result = $this->event->dispatch(self::GENERATE_EVENT);

        if ($result === null) {
            return $definitions;
        }

        $excludeUrls = $this->getExcludeUrls();

        $definitionGenerators = $this->flattenArray($result);

        foreach ($definitionGenerators as $definitionGenerator) {
            if (!($definitionGenerator instanceof Contracts\DefinitionGenerator)) {
                throw new InvalidGenerator();
            }

            $tempDefinitions = $definitionGenerator->getDefinitions();

            foreach ($tempDefinitions->getItems() as $definition) {
                if ($this->allowAdd($excludeUrls, $definition)) {
                    $definitions->addItem($definition);
                }
            }
        }

        return $definitions;
    }

    /**
     * @param string[] $excludeUrls
     * @param Dto\Definition $definition
     * @return bool
     */
    private function allowAdd(array $excludeUrls, Dto\Definition $definition): bool
    {
        return !in_array($definition->getUrl(), $excludeUrls, true);
    }

    /**
     * @return string[]
     */
    private function getExcludeUrls(): array
    {
        return $this->flattenArray($this->event->dispatch(self::EXCLUDE_URLS_EVENT));
    }

    /**
     * Flattens multi-dimensional array
     *
     * @param array $array
     * @return array
     */
    private function flattenArray(array $array): array
    {
        $flatArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flatArray = array_merge($flatArray, array_flatten($value));
            } else {
                $flatArray[$key] = $value;
            }
        }

        return $flatArray;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    private function updateCache(string $key, $value): void
    {
        if ($this->cacheForever) {
            $this->cache->forever($key, $value);
        }

        $this->cache->put($key, $value, $this->cacheTime);
    }

    /**
     * @return Dto\Definitions
     */
    private function rememberDefinitionsFromCache(): Dto\Definitions
    {
        return $this->rememberFromCache(self::CACHE_DEFINITIONS, function () {
            return $this->getDefinitions();
        });
    }

    /**
     * @param string $key
     * @param Closure $closure
     * @return mixed
     */
    private function rememberFromCache(string $key, Closure $closure)
    {
        if ($this->cacheForever) {
            return $this->cache->rememberForever($key, $closure);
        }

        return $this->cache->remember($key, $this->cacheTime, $closure);
    }

    /**
     * @return bool
     */
    private function invalidateSitemapCache(): bool
    {
        return $this->cache->forget(self::CACHE_KEY_SITEMAP);
    }

    /**
     * @return bool
     */
    private function invalidateDefinitionsCache(): bool
    {
        return $this->cache->forget(self::CACHE_DEFINITIONS);
    }
}
