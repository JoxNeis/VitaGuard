<?php

// tests/Unit/Data/Service/WeeklyScheduleTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\WeeklySchedule;
use App\Data\Service\DoctorSchedule;
use App\Data\Service\Facility;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use App\Data\Value\Schedule\DayOfWeek;
use App\Data\Value\Schedule\ScheduleType;
use Carbon\Carbon;
use InvalidArgumentException;

class WeeklyScheduleTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    private function createValidFacility(): Facility
    {
        $address = $this->createValidAddress();
        return new Facility(1, 'Test Hospital', $address, '+9171234567', 4.5);
    }

    private function createValidDoctorSchedule(DayOfWeek $day, string $open, string $close): DoctorSchedule
    {
        $facility = $this->createValidFacility();

        return new DoctorSchedule(
            1,
            $day,
            Carbon::parse($open),
            Carbon::parse($close),
            $facility,
            'Test schedule',
            30,
            20,
            500.00,
            null,
            null
        );
    }

    public function testCanCreateEmptyWeeklySchedule(): void
    {
        $weeklySchedule = new WeeklySchedule(null);

        $this->assertNull($weeklySchedule->getSchedules());
    }

    public function testCanCreateWeeklyScheduleWithSchedules(): void
    {
        $schedule1 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '09:00:00', '12:00:00');
        $schedule2 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '13:00:00', '17:00:00');

        $weeklySchedule = new WeeklySchedule([$schedule1, $schedule2]);

        $schedules = $weeklySchedule->getSchedules();

        $this->assertIsArray($schedules);

        // Count total schedules across all days
        $totalSchedules = 0;
        foreach ($schedules as $daySchedules) {
            $totalSchedules += count($daySchedules);
        }
        $this->assertEquals(2, $totalSchedules);

        // Or use array_sum
        $this->assertEquals(2, array_sum(array_map('count', $schedules)));
    }

    public function testAddSchedule(): void
    {
        $weeklySchedule = new WeeklySchedule(null);
        $schedule = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '09:00:00', '12:00:00');

        $weeklySchedule->addSchedule($schedule);

        $schedules = $weeklySchedule->getSchedules();
        $this->assertIsArray($schedules);
        $this->assertCount(1, $schedules);
    }

    public function testAddScheduleThrowsExceptionForOverlap(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The schedule overlaps with an existing time slot');

        $schedule1 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '09:00:00', '12:00:00');
        $schedule2 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '10:00:00', '13:00:00');

        $weeklySchedule = new WeeklySchedule([$schedule1]);
        $weeklySchedule->addSchedule($schedule2);
    }

    public function testAddScheduleDoesNotThrowForNonOverlappingSchedules(): void
    {
        $schedule1 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '09:00:00', '12:00:00');
        $schedule2 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '13:00:00', '17:00:00');

        $weeklySchedule = new WeeklySchedule([$schedule1]);
        $weeklySchedule->addSchedule($schedule2);
        $schedules = $weeklySchedule->getSchedules();
        $totalSchedules = 0;
        foreach ($schedules as $daySchedules) {
            $totalSchedules += count($daySchedules);
        }
        $this->assertEquals(2, $totalSchedules);
        $this->assertEquals(2, array_sum(array_map('count', $weeklySchedule->getSchedules())));
    }

    public function testAddScheduleDoesNotThrowForDifferentDays(): void
    {
        $schedule1 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '09:00:00', '12:00:00');
        $schedule2 = $this->createValidDoctorSchedule(DayOfWeek::TUESDAY, '09:00:00', '12:00:00');

        $weeklySchedule = new WeeklySchedule([$schedule1]);
        $weeklySchedule->addSchedule($schedule2);

        $this->assertCount(2, $weeklySchedule->getSchedules());
    }

    public function testSetSchedulesThrowsExceptionForInvalidItems(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All items in schedules must be instances of DailySchedule.');

        new WeeklySchedule(['invalid_item']);
    }

    public function testToArrayReturnsNullForEmptySchedules(): void
    {
        $weeklySchedule = new WeeklySchedule(null);
        $array = $weeklySchedule->toArray();

        $this->assertEquals(['schedules' => null], $array);
    }

    public function testToArrayReturnsFlattenedSchedules(): void
    {
        $schedule1 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '09:00:00', '12:00:00');
        $schedule2 = $this->createValidDoctorSchedule(DayOfWeek::MONDAY, '13:00:00', '17:00:00');

        $weeklySchedule = new WeeklySchedule([$schedule1, $schedule2]);
        $array = $weeklySchedule->toArray();

        $this->assertArrayHasKey('schedules', $array);
        $this->assertCount(2, $array['schedules']);
    }

    public function testFromArrayCreatesWeeklySchedule(): void
    {
        $addressData = [
            'detail' => '123 Test Street',
            'district' => [
                'id' => 1,
                'name' => 'Poblacion',
                'city' => [
                    'id' => 1,
                    'name' => 'Makati City',
                    'province' => ['id' => 1, 'name' => 'Metro Manila']
                ]
            ]
        ];

        $facilityData = [
            'id' => 1,
            'name' => 'Test Hospital',
            'address' => $addressData,
            'phone_number' => '+9171234567',
            'rating' => 4.5
        ];

        $scheduleData = [
            'id' => 1,
            'day' => 'never',
            'open_time' => '09:00:00',
            'close_time' => '12:00:00',
            'break_start' => null,
            'break_end' => null,
            'facility' => $facilityData,
            'notes' => 'Test',
            'slot_duration_minutes' => 30,
            'max_patients' => 20,
            'consultation_fee' => 500.00
        ];

        $data = [
            'schedules' => [$scheduleData]
        ];

        $weeklySchedule = WeeklySchedule::fromArray($data, ScheduleType::DOCTOR);

        $this->assertInstanceOf(WeeklySchedule::class, $weeklySchedule);
        $this->assertCount(1, $weeklySchedule->getSchedules());
    }

    public function testFromArrayCreatesEmptyWeeklySchedule(): void
    {
        $data = ['schedules' => null];
        $weeklySchedule = WeeklySchedule::fromArray($data, ScheduleType::DOCTOR);

        $this->assertNull($weeklySchedule->getSchedules());
    }
}