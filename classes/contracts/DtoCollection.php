<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

use Vdlp\Sitemap\Classes\Exceptions\DtoNotAccepted;

interface DtoCollection
{
    /**
     * @throws DtoNotAccepted
     */
    public function addItem(Dto $item): void;

    /**
     * @return Dto[]
     */
    public function getItems(): array;
}
