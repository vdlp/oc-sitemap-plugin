<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Exceptions;

use InvalidArgumentException;
use Throwable;

final class InvalidGenerator extends InvalidArgumentException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Invalid generator', $code, $previous);
    }
}
