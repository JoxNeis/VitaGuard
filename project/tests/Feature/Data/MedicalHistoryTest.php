<?php

// tests/Unit/Data/Medic/MedicalHistoryTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\MedicalHistory;
use App\Data\Account\Member;
use App\Data\Account\User;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

class MedicalHistoryTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
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

    private function createValidCreator(): User
    {
        $address = $this->createValidAddress();

        return new Member(
            'doctor_user',
            'doctor@example.com',
            '+9171234567',
            Status::ACTIVE,
            'Jane',
            '',
            'Smith',
            Gender::FEMALE,
            Carbon::parse('1985-01-01'),
            $address
        );
    }

    private function createValidMedicalHistory(): MedicalHistory
    {
        $creator = $this->createValidCreator();
        $patient = $this->createValidMember();

        return new MedicalHistory(
            1,
            'Patient has history of hypertension',
            $creator,
            $patient,
            Carbon::parse('2020-01-15')
        );
    }

    public function testCanCreateValidMedicalHistory(): void
    {
        $history = $this->createValidMedicalHistory();

        $this->assertEquals(1, $history->getId());
        $this->assertEquals('Patient has history of hypertension', $history->getDescription());
        $this->assertInstanceOf(User::class, $history->getCreator());
        $this->assertInstanceOf(Member::class, $history->getPatient());
        $this->assertEquals('2020-01-15', $history->getDiagnosedDate()->toDateString());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be a positive integer.');

        $creator = $this->createValidCreator();
        $patient = $this->createValidMember();

        new MedicalHistory(0, 'Description', $creator, $patient, Carbon::now());
    }

    public function testSetDescriptionThrowsExceptionForEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Description cannot be empty.');

        $creator = $this->createValidCreator();
        $patient = $this->createValidMember();

        new MedicalHistory(1, '', $creator, $patient, Carbon::now());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $history = $this->createValidMedicalHistory();
        $array = $history->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('creator', $array);
        $this->assertArrayHasKey('patient', $array);
        $this->assertArrayHasKey('diagnosed_date', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Patient has history of hypertension', $array['description']);
    }

    public function testFromArrayCreatesValidMedicalHistory(): void
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

        $creatorData = [
            'username' => 'doctor_user',
            'email' => 'doctor@example.com',
            'phone_number' => '+9171234567',
            'role' => 'member',
            'status' => 'active',
            'first_name' => 'Jane',
            'middle_name' => '',
            'last_name' => 'Smith',
            'gender' => 'female',
            'date_of_birth' => '1985-01-01 00:00:00',
            'address' => $addressData
        ];

        $patientData = [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'phone_number' => '+9171234567',
            'role' => 'member',
            'status' => 'active',
            'first_name' => 'John',
            'middle_name' => '',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01 00:00:00',
            'address' => $addressData
        ];

        $data = [
            'id' => 1,
            'description' => 'Hypertension history',
            'creator' => $creatorData,
            'patient' => $patientData,
            'diagnosed_date' => '2020-01-15 00:00:00'
        ];

        $history = MedicalHistory::fromArray($data);

        $this->assertInstanceOf(MedicalHistory::class, $history);
        $this->assertEquals(1, $history->getId());
        $this->assertEquals('Hypertension history', $history->getDescription());
    }
}