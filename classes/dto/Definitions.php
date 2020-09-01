<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Dto;

use Vdlp\Sitemap\Classes\Contracts\Dto;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotFound;
use Vdlp\Sitemap\Classes\Exceptions\InvalidPriority;

final class Definitions extends Collection
{
    public function addItem(Dto $item): void
    {
        if (!($item instanceof Definition)) {
            throw DtoNotAccepted::withDto($item);
        }

        $this->items[] = $item;
    }

    /**
     * @throws DtoNotAccepted|InvalidPriority
     */
    public static function fromArray(array $items): Definitions
    {
        return new static(array_map(static function (array $item) {
            return Definition::fromArray($item);
        }, $items));
    }

    /**
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
