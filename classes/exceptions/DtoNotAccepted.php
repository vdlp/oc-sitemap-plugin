<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Exceptions;

use InvalidArgumentException;
use Vdlp\Sitemap\Classes\Contracts\Dto;

/**
 * Class DtoNotAccepted
 *
 * @package Vdlp\Sitemap\Classes\Dto\Exceptions
 */
final class DtoNotAccepted extends InvalidArgumentException
{
    /**
     * @param Dto $dto
     * @return DtoNotAccepted
     */
    public static function withDto(Dto $dto): DtoNotAccepted
    {
        return new self('DTO of type ' . get_class($dto) . ' not accepted.');
    }
}
