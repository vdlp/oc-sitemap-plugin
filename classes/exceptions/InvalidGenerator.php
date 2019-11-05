<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Exceptions;

use InvalidArgumentException;
use Throwable;

/**
 * Class InvalidGenerator
 *
 * @package Vdlp\Sitemap\Classes\Exceptions
 */
final class InvalidGenerator extends InvalidArgumentException
{
    /**
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid generator', $code, $previous);
    }
}
