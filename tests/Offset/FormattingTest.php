<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class FormattingTest extends TestCase
{
    private Offset $offset;
    private Offset $emptyOffset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->offset = Offset::of(1, 2, 3);
        $this->emptyOffset = Offset::zero();
    }

    public function testHours(): void
    {
        self::assertSame(1, $this->offset->hours());
        self::assertSame('1', $this->offset->format('%h')->orFail());
        self::assertSame('01', $this->offset->format('%H')->orFail());

        self::assertSame(0, $this->emptyOffset->hours());
        self::assertSame('0', $this->emptyOffset->format('%h')->orFail());
        self::assertSame('00', $this->emptyOffset->format('%H')->orFail());
    }

    public function testMinutes(): void
    {
        self::assertSame(2, $this->offset->minutes());
        self::assertSame('2', $this->offset->format('%i')->orFail());
        self::assertSame('02', $this->offset->format('%I')->orFail());

        self::assertSame(0, $this->emptyOffset->minutes());
        self::assertSame('0', $this->emptyOffset->format('%i')->orFail());
        self::assertSame('00', $this->emptyOffset->format('%I')->orFail());
    }

    public function testSeconds(): void
    {
        self::assertSame(3, $this->offset->seconds());
        self::assertSame('3', $this->offset->format('%s')->orFail());
        self::assertSame('03', $this->offset->format('%S')->orFail());

        self::assertSame(0, $this->emptyOffset->seconds());
        self::assertSame('0', $this->emptyOffset->format('%s')->orFail());
        self::assertSame('00', $this->emptyOffset->format('%S')->orFail());
    }

    public function testAll(): void
    {
        self::assertSame(
            '1h 2i 3s',
            $this->offset->format('%hh %ii %ss')->orFail()
        );
        self::assertSame(
            '01H 02I 03S',
            $this->offset->format('%HH %II %SS')->orFail()
        );
        self::assertSame(
            '0h 0i 0s',
            $this->emptyOffset->format('%hh %ii %ss')->orFail()
        );
        self::assertSame(
            '00H 00I 00S',
            $this->emptyOffset->format('%HH %II %SS')->orFail()
        );
    }
}
