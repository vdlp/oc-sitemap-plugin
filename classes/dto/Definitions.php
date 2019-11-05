<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Dto;

use Vdlp\Sitemap\Classes\Contracts\Dto;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotFound;

/**
 * Class Definitions
 *
 * @package Vdlp\Sitemap\Classes\Dto
 */
final class Definitions extends Collection
{
    /**
     * {@inheritDoc}
     */
    public function addItem(Dto $item): void
    {
        if (!($item instanceof Definition)) {
            throw DtoNotAccepted::withDto($item);
        }

        $this->items[] = $item;
    }

    /**
     * @param array $items
     * @return Definitions
     * @throws DtoNotAccepted
     */
    public static function fromArray(array $items): Definitions
    {
        return new static(array_map(static function (array $item) {
            return Definition::fromArray($item);
        }, $items));
    }

    /**
     * @param string $url
     * @return void
     * @throws DtoNotFound
     */
    public function removeDefinitionByUrl(string $url): void
    {
        /** @var Definition $item */
        foreach ($this->items as $key => $item) {
            if ($item->getUrl() === $url) {
                unset($this->items[$key]);
                return;
            }
        }

        throw new DtoNotFound();
    }
}
