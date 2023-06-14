<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Exceptions;

use InvalidArgumentException;
use Throwable;

final class CountryException extends InvalidArgumentException
{
    private readonly string $countryCode;

    public function __construct(
        string $code,
        ?Throwable $previous = null,
    ) {
        $message = "Unknown or bad country ({$code})";

        parent::__construct($message, 0, $previous);
        $this->countryCode = $code;
    }

    public function code(): string
    {
        return $this->countryCode;
    }
}
