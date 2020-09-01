<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Exceptions;

use InvalidArgumentException;
use Vdlp\Sitemap\Classes\Contracts\Dto;

final class DtoNotAccepted extends InvalidArgumentException
{
    /**
     * @return DtoNotAccepted
     */
    public static function withDto(Dto $dto): DtoNotAccepted
    {
        return new self('DTO of type ' . get_class($dto) . ' not accepted.');
    }
}
