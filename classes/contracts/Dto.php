<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Contracts;

interface Dto
{
    public static function fromArray(array $data): Dto;
}
