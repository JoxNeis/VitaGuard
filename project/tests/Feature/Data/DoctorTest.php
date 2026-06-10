<?php

// tests/Unit/Data/Account/DoctorTest.php

namespace Tests\Unit\Data\Account;

use Tests\TestCase;
use App\Data\Account\Doctor;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use App\Data\Medic\Specialty;
use Carbon\Carbon;
use InvalidArgumentException;

class DoctorTest extends TestCase
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
            'Michael',
            'Doe',
            'Jr.',
            4.5,
            Gender::MALE,
            Carbon::parse('1980-05-15'),
            $address,
            [$specialty]
        );
    }

    public function testCanCreateValidDoctor(): void
    {
        $doctor = $this->createValidDoctor();

        $this->assertEquals('dr_john', $doctor->getUsername());
        $this->assertEquals('dr.john@example.com', $doctor->getEmail());
        $this->assertEquals('Dr.', $doctor->getPrefixName());
        $this->assertEquals('John', $doctor->getFirstName());
        $this->assertEquals('Michael', $doctor->getMiddleName());
        $this->assertEquals('Doe', $doctor->getLastName());
        $this->assertEquals('Jr.', $doctor->getSuffixName());
        $this->assertEquals(4.5, $doctor->getRating());
        $this->assertEquals(Gender::MALE, $doctor->getGender());
        $this->assertInstanceOf(Carbon::class, $doctor->getDateOfBirth());
        $this->assertCount(1, $doctor->getSpecialties());
    }

    public function testSetFirstNameThrowsExceptionForEmptyFirstName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty.');

        $address = $this->createValidAddress();
        new Doctor(
            'dr_john',
            'dr.john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'Dr.',
            '',
            'Michael',
            'Doe',
            'Jr.',
            4.5,
            Gender::MALE,
            Carbon::parse('1980-05-15'),
            $address,
            []
        );
    }

    public function testSetLastNameThrowsExceptionForEmptyLastName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty.');

        $address = $this->createValidAddress();
        new Doctor(
            'dr_john',
            'dr.john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'Dr.',
            'John',
            'Michael',
            '',
            'Jr.',
            4.5,
            Gender::MALE,
            Carbon::parse('1980-05-15'),
            $address,
            []
        );
    }

    public function testSetPrefixNameThrowsExceptionForEmptyPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prefix name cannot be empty.');

        $address = $this->createValidAddress();
        new Doctor(
            'dr_john',
            'dr.john@example.com',
            '+9171234567',
            Status::ACTIVE,
            '',
            'John',
            'Michael',
            'Doe',
            'Jr.',
            4.5,
            Gender::MALE,
            Carbon::parse('1980-05-15'),
            $address,
            []
        );
    }

    public function testSetRatingThrowsExceptionForNegativeRating(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rating cannot be negative.');

        $address = $this->createValidAddress();
        new Doctor(
            'dr_john',
            'dr.john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'Dr.',
            'John',
            'Michael',
            'Doe',
            'Jr.',
            -1.0,
            Gender::MALE,
            Carbon::parse('1980-05-15'),
            $address,
            []
        );
    }

    public function testAddSpecialty(): void
    {
        $doctor = $this->createValidDoctor();
        $newSpecialty = new Specialty(2, 'Neurology');
        $doctor->addSpecialty($newSpecialty);

        $this->assertCount(2, $doctor->getSpecialties());
        $this->assertSame($newSpecialty, $doctor->getSpecialties()[1]);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $doctor = $this->createValidDoctor();
        $array = $doctor->toArray();

        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('prefix_name', $array);
        $this->assertArrayHasKey('first_name', $array);
        $this->assertArrayHasKey('last_name', $array);
        $this->assertArrayHasKey('rating', $array);
        $this->assertArrayHasKey('gender', $array);
        $this->assertArrayHasKey('date_of_birth', $array);
        $this->assertArrayHasKey('address', $array);
        $this->assertEquals('dr_john', $array['username']);
        $this->assertEquals('Dr.', $array['prefix_name']);
        $this->assertEquals('John', $array['first_name']);
    }

    public function testFromArrayCreatesValidDoctor(): void
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

        $data = [
            'username' => 'dr_john',
            'email' => 'dr.john@example.com',
            'phone_number' => '+9171234567',
            'status' => 'active',
            'prefix_name' => 'Dr.',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'suffix_name' => 'Jr.',
            'rating' => '4.5',
            'gender' => 'male',
            'date_of_birth' => '1980-05-15 00:00:00',
            'address' => $addressData,
            'specialties' => [],
            'role' => 'doctor'
        ];

        $doctor = Doctor::fromArray($data);

        $this->assertInstanceOf(Doctor::class, $doctor);
        $this->assertEquals('dr_john', $doctor->getUsername());
        $this->assertEquals('Dr.', $doctor->getPrefixName());
        $this->assertEquals('John', $doctor->getFirstName());
    }
}