<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Exceptions;

use InvalidArgumentException;
use Throwable;

/**
 * Class DtoNotFound
 *
 * @package Vdlp\Sitemap\Classes\Dto\Exceptions
 */
final class DtoNotFound extends InvalidArgumentException
{
    /**
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Dto not found', $code, $previous);
    }
}
