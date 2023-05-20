<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Exceptions;

use InvalidArgumentException;
use Throwable;

final class TimeZoneException extends InvalidArgumentException
{
    public function __construct(
        private readonly string $name,
        ?Throwable $previous = null,
    ) {
        $message = "Unknown or bad time-zone ({$name})";

        parent::__construct($message, 0, $previous);
    }

    public function name(): string
    {
        return $this->name;
    }
}
