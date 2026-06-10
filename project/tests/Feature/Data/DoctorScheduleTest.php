<?php

// tests/Unit/Data/Service/DoctorScheduleTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\DoctorSchedule;
use App\Data\Service\Facility;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use App\Data\Value\Schedule\DayOfWeek;
use Carbon\Carbon;

class DoctorScheduleTest extends TestCase
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

    private function createValidDoctorSchedule(): DoctorSchedule
    {
        $facility = $this->createValidFacility();

        return new DoctorSchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            $facility,
            'Regular clinic hours',
            30,
            20,
            500.00,
            null,
            null
        );
    }

    public function testCanCreateValidDoctorSchedule(): void
    {
        $schedule = $this->createValidDoctorSchedule();

        $this->assertEquals(1, $schedule->getId());
        $this->assertEquals(DayOfWeek::MONDAY, $schedule->getDay());
        $this->assertEquals('09:00:00', $schedule->getOpen()->toTimeString());
        $this->assertEquals('17:00:00', $schedule->getClose()->toTimeString());
        $this->assertInstanceOf(Facility::class, $schedule->getFacility());
        $this->assertEquals('Regular clinic hours', $schedule->getNotes());
        $this->assertEquals(30, $schedule->getslotDurationMinutes());
        $this->assertEquals(20, $schedule->getMaxPatients());
        $this->assertEquals(500.00, $schedule->getConsultationFee());
    }

    public function testCanCreateDoctorScheduleWithNullableFields(): void
    {
        $facility = $this->createValidFacility();

        $schedule = new DoctorSchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            $facility,
            'Regular clinic hours',
            null,
            null,
            null
        );

        $this->assertNull($schedule->getslotDurationMinutes());
        $this->assertNull($schedule->getMaxPatients());
        $this->assertNull($schedule->getConsultationFee());
    }

    public function testCanCreateDoctorScheduleWithBreak(): void
    {
        $facility = $this->createValidFacility();

        $schedule = new DoctorSchedule(
            1,
            DayOfWeek::MONDAY,
            Carbon::parse('09:00:00'),
            Carbon::parse('17:00:00'),
            $facility,
            'Regular clinic hours',
            30,
            20,
            500.00,
            Carbon::parse('12:00:00'),
            Carbon::parse('13:00:00')
        );

        $this->assertEquals('12:00:00', $schedule->getBreakStart()->toTimeString());
        $this->assertEquals('13:00:00', $schedule->getBreakEnd()->toTimeString());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $schedule = $this->createValidDoctorSchedule();
        $array = $schedule->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('day', $array);
        $this->assertArrayHasKey('open_time', $array);
        $this->assertArrayHasKey('close_time', $array);
        $this->assertArrayHasKey('facility', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('slot_duration_minutes', $array);
        $this->assertArrayHasKey('max_patients', $array);
        $this->assertArrayHasKey('consultation_fee', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Regular clinic hours', $array['notes']);
    }

    public function testFromArrayCreatesValidDoctorSchedule(): void
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

        $data = [
            'id' => '1',
            'day' => 'never',
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
            'break_start' => '',
            'break_end' => '',
            'facility' => $facilityData,
            'notes' => 'Regular clinic hours',
            'slot_duration_minutes' => '30',
            'max_patients' => '20',
            'consultation_fee' => '500.00'
        ];

        $schedule = DoctorSchedule::fromArray($data);

        $this->assertInstanceOf(DoctorSchedule::class, $schedule);
        $this->assertEquals(1, $schedule->getId());
        $this->assertEquals('Test Hospital', $schedule->getFacility()->getName());
        $this->assertEquals(30, $schedule->getslotDurationMinutes());
    }

    public function testSettersAndGetters(): void
    {
        $schedule = $this->createValidDoctorSchedule();
        
        $schedule->setNotes('Updated notes');
        $schedule->setSlotDurationMinutes(45);
        $schedule->setMaxPatients(25);
        $schedule->setConsultationFee(600.00);
        
        $this->assertEquals('Updated notes', $schedule->getNotes());
        $this->assertEquals(45, $schedule->getslotDurationMinutes());
        $this->assertEquals(25, $schedule->getMaxPatients());
        $this->assertEquals(600.00, $schedule->getConsultationFee());
    }
}