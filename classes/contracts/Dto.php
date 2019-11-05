<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

/**
 * Interface Dto
 *
 * @package Vdlp\Sitemap\Classes\Contracts
 */
interface Dto
{
    /**
     * Creates an instance from an array.
     *
     * @param array $data
     * @return Dto
     */
    public static function fromArray(array $data): Dto;
}
