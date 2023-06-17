<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @internal
 */
interface Formattable
{
    /**
     * @param string|array<int, string> $format
     */
    public static function parse(
        string $string,
        string|array $format,
    ): Ok|Error;

    public function format(string $format): Ok|Error;

    public function formatted(string $format): string;
}
