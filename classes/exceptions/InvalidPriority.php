<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Exceptions;

use InvalidArgumentException;
use Throwable;

/**
 * Class InvalidPriority
 *
 * @package Vdlp\Sitemap\Classes\Exceptions
 */
final class InvalidPriority extends InvalidArgumentException
{
    /**
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid priority, allowed values are from 1 to 10', $code, $previous);
    }
}
