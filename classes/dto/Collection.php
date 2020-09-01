<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Dto;

use Countable;
use Vdlp\Sitemap\Classes\Contracts;
use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;

abstract class Collection implements Contracts\DtoCollection, Countable
{
    /**
     * @var Contracts\Dto[]
     */
    protected $items;

    /**
     * @param Contracts\Dto[] $items
     * @throws DtoNotAccepted
     */
    final public function __construct(array $items = [])
    {
        $this->items = [];

        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    abstract public function addItem(Contracts\Dto $item): void;

    /**
     * @return Contracts\Dto[]
     */
    final public function getItems(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        return $this->items[count($this) - 1] ?? null;
    }
}
