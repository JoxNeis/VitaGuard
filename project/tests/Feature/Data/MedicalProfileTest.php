<?php

// tests/Unit/Data/Medic/MedicalProfileTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\MedicalProfile;
use App\Data\Account\Member;
use App\Data\Account\User;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Value\Medic\BloodType;
use App\Data\Value\Medic\SmokingStatus;
use App\Data\Value\Medic\AlcoholConsumption;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

class MedicalProfileTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
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

    private function createValidPatient(): Member
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

    private function createValidMedicalProfile(): MedicalProfile
    {
        $creator = $this->createValidCreator();
        $patient = $this->createValidPatient();

        return new MedicalProfile(
            1,
            'Patient is in good health',
            BloodType::O_POSITIVE,
            175.5,
            72.3,
            SmokingStatus::NEVER,
            AlcoholConsumption::LIGHT,
            $creator,
            $patient,
            Carbon::parse('2024-01-15')
        );
    }

    public function testCanCreateValidMedicalProfile(): void
    {
        $profile = $this->createValidMedicalProfile();

        $this->assertEquals(1, $profile->getId());
        $this->assertEquals('Patient is in good health', $profile->getDescription());
        $this->assertEquals(BloodType::O_POSITIVE, $profile->getBloodType());
        $this->assertEquals(175.5, $profile->getHeight());
        $this->assertEquals(72.3, $profile->getWeight());
        $this->assertEquals(SmokingStatus::NEVER, $profile->getSmokingStatus());
        $this->assertEquals(AlcoholConsumption::LIGHT, $profile->getAlcoholConsumption());
        $this->assertInstanceOf(User::class, $profile->getCreator());
        $this->assertInstanceOf(Member::class, $profile->getPatient());
        $this->assertEquals('2024-01-15', $profile->getDiagnosedDate()->toDateString());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be a positive integer.');

        $creator = $this->createValidCreator();
        $patient = $this->createValidPatient();

        new MedicalProfile(0, 'Description', BloodType::O_POSITIVE, 175, 72, SmokingStatus::NEVER, AlcoholConsumption::NONE, $creator, $patient, Carbon::now());
    }

    public function testSetDescriptionThrowsExceptionForEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Description cannot be empty.');

        $creator = $this->createValidCreator();
        $patient = $this->createValidPatient();

        new MedicalProfile(1, '', BloodType::O_POSITIVE, 175, 72, SmokingStatus::NEVER, AlcoholConsumption::NONE, $creator, $patient, Carbon::now());
    }

    public function testSetHeightThrowsExceptionForNonPositiveHeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Height must be greater than zero.');

        $creator = $this->createValidCreator();
        $patient = $this->createValidPatient();

        new MedicalProfile(1, 'Description', BloodType::O_POSITIVE, 0, 72, SmokingStatus::NEVER, AlcoholConsumption::NONE, $creator, $patient, Carbon::now());
    }

    public function testSetWeightThrowsExceptionForNonPositiveWeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Weight must be greater than zero.');

        $creator = $this->createValidCreator();
        $patient = $this->createValidPatient();

        new MedicalProfile(1, 'Description', BloodType::O_POSITIVE, 175, 0, SmokingStatus::NEVER, AlcoholConsumption::NONE, $creator, $patient, Carbon::now());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $profile = $this->createValidMedicalProfile();
        $array = $profile->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('blood_type', $array);
        $this->assertArrayHasKey('height', $array);
        $this->assertArrayHasKey('weight', $array);
        $this->assertArrayHasKey('smoking_status', $array);
        $this->assertArrayHasKey('alcohol_consumption', $array);
        $this->assertArrayHasKey('creator', $array);
        $this->assertArrayHasKey('patient', $array);
        $this->assertArrayHasKey('diagnosed_date', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('O+', $array['blood_type']);
    }

    public function testFromArrayCreatesValidMedicalProfile(): void
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
            'description' => 'Patient health profile',
            'blood_type' => 'O+',
            'height' => 175.5,
            'weight' => 72.3,
            'smoking_status' => 'never',
            'alcohol_consumption' => 'light',
            'creator' => $creatorData,
            'patient' => $patientData,
            'diagnosed_date' => '2024-01-15 00:00:00'
        ];

        $profile = MedicalProfile::fromArray($data);

        $this->assertInstanceOf(MedicalProfile::class, $profile);
        $this->assertEquals(1, $profile->getId());
        $this->assertEquals('Patient health profile', $profile->getDescription());
    }
}