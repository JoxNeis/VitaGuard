<?php

// tests/Unit/Data/Service/DailyScheduleTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\DailySchedule;
use App\Data\Value\Schedule\DayOfWeek;
use Carbon\Carbon;
use InvalidArgumentException;

// Concrete implementation for testing abstract class
class TestDailySchedule extends DailySchedule
{
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            DayOfWeek::from($data['day']),
            Carbon::parse($data['open_time']),
            Carbon::parse($data['close_time']),
            isset($data['break_start_time']) ? Carbon::parse($data['break_start_time']) : null,
            isset($data['break_end_time']) ? Carbon::parse($data['break_end_time']) : null
        );
    }
}

class DailyScheduleTest extends TestCase
{
    private function createValidSchedule(): TestDailySchedule
    {
        return new TestDailySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            null,
            null
        );
    }

    public function testCanCreateValidDailySchedule(): void
    {
        $schedule = $this->createValidSchedule();

        $this->assertEquals(1, $schedule->getId());
        $this->assertEquals(DayOfWeek::MONDAY, $schedule->getDay());
        $this->assertEquals('09:00:00', $schedule->getOpen()->toTimeString());
        $this->assertEquals('17:00:00', $schedule->getClose()->toTimeString());
        $this->assertNull($schedule->getBreakStart());
        $this->assertNull($schedule->getBreakEnd());
    }

    public function testSetCloseThrowsExceptionWhenCloseTimeIsBeforeOpenTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Close time must be after open time.');

        new TestDailySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('08:00:00'),
            null,
            null
        );
    }

    public function testSetCloseThrowsExceptionWhenCloseTimeEqualsOpenTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Close time must be after open time.');

        new TestDailySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('09:00:00'),
            null,
            null
        );
    }

    public function testCanCreateScheduleWithBreak(): void
    {
        $schedule = new TestDailySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            Carbon::parse('12:00:00'),
            Carbon::parse('13:00:00')
        );

        $this->assertEquals('12:00:00', $schedule->getBreakStart()->toTimeString());
        $this->assertEquals('13:00:00', $schedule->getBreakEnd()->toTimeString());
    }

    public function testSetBreakStartThrowsExceptionWhenBreakStartsBeforeOpen(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Break cannot start before opening time.');

        $schedule = new TestDailySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            Carbon::parse('08:00:00'),
            null
        );
    }

    public function testSetBreakEndThrowsExceptionWhenBreakEndIsBeforeBreakStart(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Break end time must be after break start time.');

        new TestDailySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            Carbon::parse('13:00:00'),
            Carbon::parse('12:00:00')
        );
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $schedule = $this->createValidSchedule();
        $array = $schedule->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('day', $array);
        $this->assertArrayHasKey('open_time', $array);
        $this->assertArrayHasKey('close_time', $array);
        $this->assertArrayHasKey('break_start_time', $array);
        $this->assertArrayHasKey('break_end_time', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('never', $array['day']);
    }
}