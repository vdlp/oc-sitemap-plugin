<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Vdlp\Sitemap\Classes\Contracts\ConfigResolver;
use Vdlp\Sitemap\Classes\Contracts\DefinitionGenerator;
use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator as SitemapGeneratorInterface;
use Vdlp\Sitemap\Classes\Dto\Definition;
use Vdlp\Sitemap\Classes\Dto\Definitions;
use Vdlp\Sitemap\Classes\Dto\SitemapConfig;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotFound;
use Vdlp\Sitemap\Classes\Exceptions\InvalidGenerator;

final class SitemapGenerator implements SitemapGeneratorInterface
{
    private Repository $cache;

    private Dispatcher $event;

    private SitemapConfig $sitemapConfig;

    public function __construct(Repository $cache, Dispatcher $event, ConfigResolver $configResolver)
    {
        $this->cache = $cache;
        $this->event = $event;
        $this->sitemapConfig = $configResolver->getConfig();
    }

    public function invalidateCache(): bool
    {
        return $this->invalidateSitemapCache() && $this->invalidateDefinitionsCache();
    }

    /**
     * @throws RuntimeException
     */
    public function generate(): void
    {
        try {
            $fromCache = $this->cache->has($this->sitemapConfig->getCacheKeySitemap());
        } catch (InvalidArgumentException $e) {
            $fromCache = false;
        }

        $path = storage_path($this->sitemapConfig->getCacheFilePath());

        $fileExists = file_exists($path);

        if ($fromCache && !$fileExists) {
            $this->invalidateCache();
            $fromCache = false;
        }

        if ($fromCache && file_exists($path)) {
            return;
        }

        $this->createXmlFile($this->rememberDefinitionsFromCache(), $path);
        $this->updateCache($this->sitemapConfig->getCacheKeySitemap(), true);
    }

    public function output(): void
    {
        header('Content-Type: application/xml');

        $handle = fopen(storage_path($this->sitemapConfig->getCacheFilePath()), 'rb');

        if ($handle === false) {
            exit(1);
        }

        fpassthru($handle);
        fclose($handle);

        exit(0);
    }

    public function updateDefinition(Definition $definition, ?string $oldUrl = null): void
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

        $this->updateCache($this->sitemapConfig->getCacheKeyDefinitions(), $definitions);
        $this->invalidateSitemapCache();
    }

    public function addDefinition(Definition $definition): void
    {
        if (!$this->allowAdd($this->getExcludeUrls(), $definition)) {
            return;
        }

        $definitions = $this->rememberDefinitionsFromCache();
        $definitions->addItem($definition);
        $this->updateCache($this->sitemapConfig->getCacheKeyDefinitions(), $definitions);
        $this->invalidateSitemapCache();
    }

    public function updateOrAddDefinition(Definition $definition, ?string $oldUrl = null): void
    {
        try {
            $this->updateDefinition($definition, $oldUrl);
        } catch (DtoNotFound $e) {
            $this->addDefinition($definition);
        }
    }

    public function deleteDefinition(string $url): void
    {
        $definitions = $this->rememberDefinitionsFromCache();
        $definitions->removeDefinitionByUrl($url);
        $this->updateCache($this->sitemapConfig->getCacheKeyDefinitions(), $definitions);
        $this->invalidateSitemapCache();
    }

    /**
     * @throws RuntimeException
     */
    private function createXmlFile(Definitions $definitions, string $path): void
    {
        $concurrentDirectory = dirname($path);

        if (!file_exists($concurrentDirectory)) {
            $directoryCreated = mkdir($concurrentDirectory, 0777, true);

            if (!$directoryCreated) {
                throw new RuntimeException(sprintf(
                    'Vdlp.Sitemap: Directory "%s" could not be created.',
                    $concurrentDirectory
                ));
            }
        }

        if (!is_dir($concurrentDirectory) || !is_readable($concurrentDirectory)) {
            throw new RuntimeException(sprintf(
                'Vdlp.Sitemap: Unable to read directory "%s".',
                $concurrentDirectory
            ));
        }

        if (file_exists($path) && !unlink($path)) {
            throw new RuntimeException(sprintf(
                'Vdlp.Sitemap: Unable to delete file "%s".',
                $path
            ));
        }

        if (!file_exists($path) && !touch($path)) {
            throw new RuntimeException(sprintf(
                'Vdlp.Sitemap: Unable to touch file "%s".',
                $path
            ));
        }

        $file = fopen($path, 'a+b');

        if ($file === false) {
            throw new RuntimeException(sprintf(
                'Vdlp.Sitemap: Unable to open file "%s".',
                $path
            ));
        }

        fwrite($file, '<?xml version="1.0" encoding="UTF-8" ?>');
        fwrite($file, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">');

        /** @var Dto\Definition $definition */
        foreach ($definitions->getItems() as $definition) {
            $xml = '<url>';

            if ($definition->getUrl() !== null) {
                $xml .= '<loc>' . htmlspecialchars($definition->getUrl(), ENT_XML1, 'UTF-8') . '</loc>';
            }

            if ($definition->getModifiedAt() !== null) {
                $xml .= '<lastmod>' . $definition->getModifiedAt()->toAtomString() . '</lastmod>';
            }

            if ($definition->getPriorityFloat() !== null) {
                $xml .= '<priority>' . $definition->getPriorityFloat() . '</priority>';
            }

            if ($definition->getChangeFrequency() !== null) {
                $xml .= '<changefreq>' . $definition->getChangeFrequency() . '</changefreq>';
            }

            foreach ($definition->getImages() as $image) {
                $xml .= '<image:image>';
                $xml .= '<image:loc>'
                    . htmlspecialchars($image->getUrl(), ENT_XML1, 'UTF-8')
                    . '</image:loc>';

                if ($image->getTitle() !== null) {
                    $xml .= '<image:title>'
                        . htmlspecialchars($image->getTitle(), ENT_XML1, 'UTF-8')
                        . '</image:title>';
                }

                $xml .= '</image:image>';
            }

            $xml .= '</url>';

            fwrite($file, $xml);
        }

        fwrite($file, '</urlset>');
        fclose($file);
    }

    /**
     * @throws InvalidGenerator|Exceptions\DtoNotAccepted
     */
    private function getDefinitions(): Definitions
    {
        $definitions = new Definitions();

        $result = $this->event->dispatch(self::GENERATE_EVENT);

        if ($result === null) {
            return $definitions;
        }

        $excludeUrls = $this->getExcludeUrls();

        $definitionGenerators = $this->flattenArray($result);

        foreach ($definitionGenerators as $definitionGenerator) {
            if (!($definitionGenerator instanceof DefinitionGenerator)) {
                throw new InvalidGenerator();
            }

            $tempDefinitions = $definitionGenerator->getDefinitions();

            /** @var Definition $definition */
            foreach ($tempDefinitions->getItems() as $definition) {
                if ($this->allowAdd($excludeUrls, $definition)) {
                    $definitions->addItem($definition);
                }
            }
        }

        return $definitions;
    }

    private function allowAdd(array $excludeUrls, Definition $definition): bool
    {
        return !in_array($definition->getUrl(), $excludeUrls, true);
    }

    private function getExcludeUrls(): array
    {
        return $this->flattenArray((array) $this->event->dispatch(self::EXCLUDE_URLS_EVENT));
    }

    private function flattenArray(array $array): array
    {
        $flatArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $flatArray = array_merge($flatArray, array_flatten($value));
            } else {
                $flatArray[$key] = $value;
            }
        }

        return $flatArray;
    }

    private function updateCache(string $key, mixed $value): void
    {
        if ($this->sitemapConfig->isCacheForever()) {
            $this->cache->forever($key, $value);
        }

        $this->cache->put($key, $value, $this->sitemapConfig->getCacheTime());
    }

    private function rememberDefinitionsFromCache(): Definitions
    {
        /** @var Definitions $definitions */
        $definitions = $this->rememberFromCache($this->sitemapConfig->getCacheKeyDefinitions(), function (): Definitions {
            return $this->getDefinitions();
        });

        return $definitions;
    }

    private function rememberFromCache(string $key, Closure $closure): mixed
    {
        if ($this->sitemapConfig->isCacheForever()) {
            return $this->cache->rememberForever($key, $closure);
        }

        return $this->cache->remember($key, $this->sitemapConfig->getCacheTime(), $closure);
    }

    private function invalidateSitemapCache(): bool
    {
        return $this->cache->forget($this->sitemapConfig->getCacheKeySitemap());
    }

    private function invalidateDefinitionsCache(): bool
    {
        return $this->cache->forget($this->sitemapConfig->getCacheKeyDefinitions());
    }
}
