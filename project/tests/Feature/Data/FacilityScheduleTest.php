<?php

// tests/Unit/Data/Service/FacilityScheduleTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\FacilitySchedule;
use App\Data\Value\Schedule\DayOfWeek;
use Carbon\Carbon;

class FacilityScheduleTest extends TestCase
{
    public function testCanCreateValidFacilitySchedule(): void
    {
        $schedule = new FacilitySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            Carbon::parse('12:00:00'),
            Carbon::parse('13:00:00')
        );

        $this->assertEquals(1, $schedule->getId());
        $this->assertEquals(DayOfWeek::MONDAY, $schedule->getDay());
        $this->assertEquals('09:00:00', $schedule->getOpen()->toTimeString());
        $this->assertEquals('17:00:00', $schedule->getClose()->toTimeString());
        $this->assertEquals('12:00:00', $schedule->getBreakStart()->toTimeString());
        $this->assertEquals('13:00:00', $schedule->getBreakEnd()->toTimeString());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $schedule = new FacilitySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            Carbon::parse('12:00:00'),
            Carbon::parse('13:00:00')
        );

        $array = $schedule->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('day', $array);
        $this->assertArrayHasKey('open_time', $array);
        $this->assertArrayHasKey('close_time', $array);
        $this->assertArrayHasKey('break_start_time', $array);
        $this->assertArrayHasKey('break_end_time', $array);
    }

    public function testFromArrayCreatesValidFacilitySchedule(): void
    {
        $data = [
            'id' => 1,
            'day' => 'never',
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00'
        ];

        $schedule = FacilitySchedule::fromArray($data);

        $this->assertInstanceOf(FacilitySchedule::class, $schedule);
        $this->assertEquals(1, $schedule->getId());
        $this->assertEquals('09:00:00', $schedule->getOpen()->toTimeString());
    }

    public function testCanCreateScheduleWithoutBreak(): void
    {
        $schedule = new FacilitySchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            null,
            null
        );

        $this->assertNull($schedule->getBreakStart());
        $this->assertNull($schedule->getBreakEnd());
    }
}