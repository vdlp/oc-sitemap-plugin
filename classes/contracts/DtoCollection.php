<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;

/**
 * Interface DtoCollection
 *
 * @package Vdlp\Sitemap\Classes\Contracts
 */
interface DtoCollection
{
    /**
     * Add DTO item to collection.
     *
     * @param Dto $item
     * @return void
     * @throws DtoNotAccepted
     */
    public function addItem(Dto $item): void;

    /**
     * @return Dto[]
     */
    public function getItems(): array;
}
