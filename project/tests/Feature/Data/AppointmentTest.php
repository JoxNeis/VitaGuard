<?php

// tests/Unit/Data/Service/AppointmentTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\Appointment;
use App\Data\Account\Doctor;
use App\Data\Account\Member;
use App\Data\Value\Schedule\AppointmentStatus;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use App\Data\Medic\Specialty;
use Carbon\Carbon;
use InvalidArgumentException;

class AppointmentTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    private function createValidDoctor(): Doctor
    {
        $address = $this->createValidAddress();
        $specialty = new Specialty(1, 'Cardiology');

        return new Doctor(
            'dr_john',
            'dr.john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'Dr.',
            'John',
            '',
            'Doe',
            '',
            4.5,
            Gender::MALE,
            Carbon::parse('1980-01-01'),
            $address,
            [$specialty]
        );
    }

    private function createValidMember(): Member
    {
        $address = $this->createValidAddress();

        return new Member(
            'john_doe',
            'john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'John',
            '',
            'Doe',
            Gender::MALE,
            Carbon::parse('1990-01-01'),
            $address
        );
    }

    private function createValidAppointment(): Appointment
    {
        $doctor = $this->createValidDoctor();
        $patient = $this->createValidMember();

        return new Appointment(
            1,
            $doctor,
            $patient,
            Carbon::parse('2024-01-15'),
            Carbon::parse('10:00:00'),
            1,
            AppointmentStatus::PENDING,
            'Initial consultation',
            null,
            null
        );
    }

    public function testCanCreateValidAppointment(): void
    {
        $appointment = $this->createValidAppointment();

        $this->assertEquals(1, $appointment->getId());
        $this->assertInstanceOf(Doctor::class, $appointment->getDoctor());
        $this->assertInstanceOf(Member::class, $appointment->getPatient());
        $this->assertEquals('2024-01-15', $appointment->getDate()->toDateString());
        $this->assertEquals('10:00:00', $appointment->getTime()->toTimeString());
        $this->assertEquals(1, $appointment->getQueueOrder());
        $this->assertEquals(AppointmentStatus::PENDING, $appointment->getStatus());
        $this->assertEquals('Initial consultation', $appointment->getNotes());
        $this->assertNull($appointment->getCheckInTime());
        $this->assertNull($appointment->getCompletedTime());
    }

    public function testSetQueueOrderThrowsExceptionForNonPositiveValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Queue order must be a positive integer.');

        $appointment = $this->createValidAppointment();
        $appointment->setQueueOrder(0);
    }

    public function testSetQueueOrderAcceptsPositiveValue(): void
    {
        $appointment = $this->createValidAppointment();
        $appointment->setQueueOrder(5);

        $this->assertEquals(5, $appointment->getQueueOrder());
    }

    public function testSetCheckInTimeThrowsExceptionWhenBeforeAppointmentDate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Check-in time cannot be earlier than the appointment date.');

        $appointment = $this->createValidAppointment();
        $appointment->setCheckInTime(Carbon::parse('2024-01-14 09:00:00'));
    }

    public function testSetCheckInTimeAcceptsValidTime(): void
    {
        $appointment = $this->createValidAppointment();
        $check_in_time = Carbon::parse('2024-01-15 09:30:00');
        $appointment->setCheckInTime($check_in_time);

        $this->assertSame($check_in_time, $appointment->getCheckInTime());
    }

    public function testSetCompletedTimeThrowsExceptionWhenBeforeCheckInTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Completion time cannot be earlier than check-in time.');

        $appointment = $this->createValidAppointment();
        $appointment->setCheckInTime(Carbon::parse('2024-01-15 10:00:00'));
        $appointment->setCompletedTime(Carbon::parse('2024-01-15 09:30:00'));
    }

    public function testSetCompletedTimeThrowsExceptionWhenBeforeAppointmentDate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Completion time cannot be earlier than the appointment date.');

        $appointment = $this->createValidAppointment();
        $appointment->setCompletedTime(Carbon::parse('2024-01-14 10:30:00'));
    }

    public function testSetCompletedTimeAcceptsValidTime(): void
    {
        $appointment = $this->createValidAppointment();
        $completed_time = Carbon::parse('2024-01-15 10:30:00');
        $appointment->setCompletedTime($completed_time);

        $this->assertSame($completed_time, $appointment->getCompletedTime());
    }

    public function testSetStatus(): void
    {
        $appointment = $this->createValidAppointment();
        $appointment->setStatus(AppointmentStatus::CONFIRMED);

        $this->assertEquals(AppointmentStatus::CONFIRMED, $appointment->getStatus());
    }

    public function testSetNotes(): void
    {
        $appointment = $this->createValidAppointment();
        $appointment->setNotes('Updated notes');

        $this->assertEquals('Updated notes', $appointment->getNotes());
    }

    public function testSetNotesToNull(): void
    {
        $appointment = $this->createValidAppointment();
        $appointment->setNotes(null);

        $this->assertNull($appointment->getNotes());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $appointment = $this->createValidAppointment();
        $array = $appointment->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('doctor', $array);
        $this->assertArrayHasKey('patient', $array);
        $this->assertArrayHasKey('date', $array);
        $this->assertArrayHasKey('time', $array);
        $this->assertArrayHasKey('queue_order', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('pending', $array['status']);
    }

    public function testFromArrayCreatesValidAppointment(): void
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

        $doctorData = [
            'username' => 'dr_john',
            'email' => 'dr.john@example.com',
            'phone_number' => '+9171234567',
            'status' => 'active',
            'prefix_name' => 'Dr.',
            'first_name' => 'John',
            'middle_name' => '',
            'last_name' => 'Doe',
            'suffix_name' => '',
            'rating' => '4.5',
            'gender' => 'male',
            'date_of_birth' => '1980-01-01 00:00:00',
            'address' => $addressData,
            'specialties' => [],
            'role'=> 'doctor'
        ];

        $patientData = [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'phone_number' => '+9171234567',
            'status' => 'active',
            'first_name' => 'John',
            'middle_name' => '',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01 00:00:00',
            'address' => $addressData,
            'role' => 'member'
        ];

        $data = [
            'id' => '1',
            'doctor' => $doctorData,
            'patient' => $patientData,
            'date' => '2024-01-15',
            'time' => '10:00:00',
            'queue_order' => '1',
            'status' => 'pending',
            'notes' => 'Initial consultation',
            'check_in_time' => '',
            'completed_time' => ''
        ];

        $appointment = Appointment::fromArray($data);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertEquals(1, $appointment->getId());
        $this->assertEquals('Initial consultation', $appointment->getNotes());
    }
}