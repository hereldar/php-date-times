<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class FormattingTest extends TestCase
{
    private Period $period;
    private Period $emptyPeriod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->period = Period::of(1, 2, 3, 4, 5, 6, 7_000);
        $this->emptyPeriod = Period::zero();
    }

    public function testYears(): void
    {
        self::assertSame(1, $this->period->years());
        self::assertSame('1', $this->period->format('%y')->orFail());
        self::assertSame('0001', $this->period->formatted('%Y'));

        self::assertSame(0, $this->emptyPeriod->years());
        self::assertSame('0', $this->emptyPeriod->format('%y')->orFail());
        self::assertSame('0000', $this->emptyPeriod->formatted('%Y'));
    }

    public function testMonths(): void
    {
        self::assertSame(2, $this->period->months());
        self::assertSame('2', $this->period->format('%m')->orFail());
        self::assertSame('02', $this->period->formatted('%M'));

        self::assertSame(0, $this->emptyPeriod->months());
        self::assertSame('0', $this->emptyPeriod->format('%m')->orFail());
        self::assertSame('00', $this->emptyPeriod->formatted('%M'));
    }

    public function testDays(): void
    {
        self::assertSame(3, $this->period->days());
        self::assertSame('3', $this->period->format('%d')->orFail());
        self::assertSame('03', $this->period->formatted('%D'));

        self::assertSame(0, $this->emptyPeriod->days());
        self::assertSame('0', $this->emptyPeriod->format('%d')->orFail());
        self::assertSame('00', $this->emptyPeriod->formatted('%D'));
    }

    public function testWeeksAndDays(): void
    {
        $period = Period::of(weeks: 3, days: 4);

        self::assertSame(25, $period->days());
        self::assertSame('3-4', $period->format('%w-%e')->orFail());
        self::assertSame('03-04', $period->formatted('%W-%E'));

        self::assertSame(0, $this->emptyPeriod->days());
        self::assertSame('0-0', $this->emptyPeriod->format('%w-%e')->orFail());
        self::assertSame('00-00', $this->emptyPeriod->formatted('%W-%E'));
    }

    public function testHours(): void
    {
        self::assertSame(4, $this->period->hours());
        self::assertSame('4', $this->period->format('%h')->orFail());
        self::assertSame('04', $this->period->formatted('%H'));

        self::assertSame(0, $this->emptyPeriod->hours());
        self::assertSame('0', $this->emptyPeriod->format('%h')->orFail());
        self::assertSame('00', $this->emptyPeriod->formatted('%H'));
    }

    public function testMinutes(): void
    {
        self::assertSame(5, $this->period->minutes());
        self::assertSame('5', $this->period->format('%i')->orFail());
        self::assertSame('05', $this->period->formatted('%I'));

        self::assertSame(0, $this->emptyPeriod->minutes());
        self::assertSame('0', $this->emptyPeriod->format('%i')->orFail());
        self::assertSame('00', $this->emptyPeriod->formatted('%I'));
    }

    public function testSeconds(): void
    {
        self::assertSame(6, $this->period->seconds());
        self::assertSame('6', $this->period->format('%s')->orFail());
        self::assertSame('06', $this->period->formatted('%S'));

        self::assertSame(0, $this->emptyPeriod->seconds());
        self::assertSame('0', $this->emptyPeriod->format('%s')->orFail());
        self::assertSame('00', $this->emptyPeriod->formatted('%S'));
    }

    public function testFractionOfSeconds(): void
    {
        self::assertSame(7_000, $this->period->microseconds());
        self::assertSame('.007', $this->period->format('%f')->orFail());
        self::assertSame('.007000', $this->period->formatted('%F'));

        self::assertSame(0, $this->emptyPeriod->microseconds());
        self::assertSame('', $this->emptyPeriod->format('%f')->orFail());
        self::assertSame('', $this->emptyPeriod->formatted('%F'));
    }

    public function testMilliseconds(): void
    {
        $period = Period::of(milliseconds: 2);

        self::assertSame(2_000, $period->microseconds());
        self::assertSame('2', $period->format('%v')->orFail());
        self::assertSame('002', $period->formatted('%V'));

        self::assertSame(0, $this->emptyPeriod->microseconds());
        self::assertSame('0', $this->emptyPeriod->format('%v')->orFail());
        self::assertSame('000', $this->emptyPeriod->formatted('%V'));
    }

    public function testMicroseconds(): void
    {
        $period = Period::of(microseconds: 2);

        self::assertSame(2, $period->microseconds());
        self::assertSame('2', $period->format('%u')->orFail());
        self::assertSame('000002', $period->formatted('%U'));

        self::assertSame(0, $this->emptyPeriod->microseconds());
        self::assertSame('0', $this->emptyPeriod->format('%u')->orFail());
        self::assertSame('000000', $this->emptyPeriod->formatted('%U'));
    }

    public function testAll(): void
    {
        self::assertSame(
            '1y 2m 3d 4h 5i 6.007s',
            $this->period->format('%yy %mm %dd %hh %ii %s%fs')->orFail()
        );
        self::assertSame(
            '0001Y 02M 03D 04H 05I 06.007000S',
            $this->period->format('%YY %MM %DD %HH %II %S%FS')->orFail()
        );
        self::assertSame(
            '0y 0m 0d 0h 0i 0s',
            $this->emptyPeriod->formatted('%yy %mm %dd %hh %ii %s%fs')
        );
        self::assertSame(
            '0000Y 00M 00D 00H 00I 00S',
            $this->emptyPeriod->formatted('%YY %MM %DD %HH %II %S%FS')
        );
    }
}
