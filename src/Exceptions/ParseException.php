<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Exceptions;

use InvalidArgumentException;
use Throwable;

final class ParseException extends InvalidArgumentException
{
    public function __construct(
        string $string,
        string $format,
        ?Throwable $previous = null
    ) {
        $message = sprintf(
            'String %s does not match format %s',
            var_export($string, true),
            var_export($format, true),
        );

        parent::__construct($message, 0, $previous);
    }
}
