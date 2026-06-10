<?php

// tests/Unit/Data/Medic/PrescriptionTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\Prescription;
use App\Data\Account\Doctor;
use App\Data\Account\Member;
use App\Data\Service\Appointment;
use App\Data\Service\Consultation;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Value\Schedule\AppointmentStatus;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use App\Data\Medic\Specialty;
use Carbon\Carbon;
use InvalidArgumentException;

class PrescriptionTest extends TestCase
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

    private function createValidPrescription(): Prescription
    {
        $patient = $this->createValidMember();
        $doctor = $this->createValidDoctor();

        return new Prescription(
            1,
            $patient,
            $doctor,
            'Take medication as prescribed',
            null,
            null
        );
    }

    public function testCanCreateValidPrescription(): void
    {
        $prescription = $this->createValidPrescription();

        $this->assertEquals(1, $prescription->getId());
        $this->assertInstanceOf(Member::class, $prescription->getPatient());
        $this->assertInstanceOf(Doctor::class, $prescription->getDoctor());
        $this->assertEquals('Take medication as prescribed', $prescription->getNotes());
        $this->assertNull($prescription->getAppointment());
        $this->assertNull($prescription->getConsultation());
    }

    public function testCanCreatePrescriptionWithAppointment(): void
    {
        $patient = $this->createValidMember();
        $doctor = $this->createValidDoctor();
        
        $appointment = new Appointment(
            1,
            $doctor,
            $patient,
            Carbon::parse('2024-01-15'),
            Carbon::parse('10:00:00'),
            1,
            AppointmentStatus::COMPLETED,
            null,
            null,
            null
        );

        $prescription = new Prescription(
            1,
            $patient,
            $doctor,
            'Take medication as prescribed',
            $appointment,
            null
        );

        $this->assertSame($appointment, $prescription->getAppointment());
        $this->assertNull($prescription->getConsultation());
    }

    public function testCanCreatePrescriptionWithConsultation(): void
    {
        $patient = $this->createValidMember();
        $doctor = $this->createValidDoctor();
        
        $consultation = new Consultation(
            1,
            Carbon::parse('2024-01-15 10:00:00'),
            'Patient consultation',
            Carbon::parse('2024-01-15 10:30:00'),
            null
        );

        $prescription = new Prescription(
            1,
            $patient,
            $doctor,
            'Take medication as prescribed',
            null,
            $consultation
        );

        $this->assertNull($prescription->getAppointment());
        $this->assertSame($consultation, $prescription->getConsultation());
    }

    public function testSetNotesThrowsExceptionForEmptyNotes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prescription notes cannot be empty.');

        $patient = $this->createValidMember();
        $doctor = $this->createValidDoctor();

        new Prescription(1, $patient, $doctor, '');
    }

    public function testSetNotesThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prescription notes cannot be empty.');

        $patient = $this->createValidMember();
        $doctor = $this->createValidDoctor();

        new Prescription(1, $patient, $doctor, '   ');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $prescription = $this->createValidPrescription();
        $array = $prescription->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('patient', $array);
        $this->assertArrayHasKey('doctor', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('appointment', $array);
        $this->assertArrayHasKey('consultation', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Take medication as prescribed', $array['notes']);
    }

    public function testFromArrayCreatesValidPrescription(): void
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
            'role' => 'doctor'
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
            'id' => 1,
            'patient' => $patientData,
            'doctor' => $doctorData,
            'notes' => 'Take medication as prescribed',
            'appointment' => null,
            'consultation' => null
        ];

        $prescription = Prescription::fromArray($data);

        $this->assertInstanceOf(Prescription::class, $prescription);
        $this->assertEquals(1, $prescription->getId());
        $this->assertEquals('Take medication as prescribed', $prescription->getNotes());
    }
}