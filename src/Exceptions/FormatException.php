<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Exceptions;

use InvalidArgumentException;
use Throwable;

final class FormatException extends InvalidArgumentException
{
    public function __construct(
        private readonly string $format,
        private readonly ?string $error = null,
        ?Throwable $previous = null
    ) {
        $message = \sprintf(
            'Format %s is not valid',
            \var_export($format, true),
        );

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($error) {
            $message .= " ({$error})";
        }

        parent::__construct($message, 0, $previous);
    }

    public function format(): string
    {
        return $this->format;
    }

    public function error(): ?string
    {
        return $this->error;
    }
}
