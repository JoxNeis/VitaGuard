<?php

// tests/Unit/Data/Account/MemberTest.php

namespace Tests\Unit\Data\Account;

use Tests\TestCase;
use App\Data\Account\Member;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

class MemberTest extends TestCase
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
            'Michael',
            'Doe',
            Gender::MALE,
            Carbon::parse('1990-05-15'),
            $address
        );
    }

    public function testCanCreateValidMember(): void
    {
        $member = $this->createValidMember();

        $this->assertEquals('john_doe', $member->getUsername());
        $this->assertEquals('john@example.com', $member->getEmail());
        $this->assertEquals('John', $member->getFirstName());
        $this->assertEquals('Michael', $member->getMiddleName());
        $this->assertEquals('Doe', $member->getLastName());
        $this->assertEquals(Gender::MALE, $member->getGender());
        $this->assertInstanceOf(Carbon::class, $member->getDateOfBirth());
        $this->assertInstanceOf(Address::class, $member->getAddress());
    }

    public function testSetFirstNameThrowsExceptionForEmptyFirstName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty.');

        $address = $this->createValidAddress();
        new Member(
            'john_doe',
            'john@example.com',
            '+9171234567',
            Status::ACTIVE,
            '',
            'Michael',
            'Doe',
            Gender::MALE,
            Carbon::parse('1990-05-15'),
            $address
        );
    }

    public function testSetLastNameThrowsExceptionForEmptyLastName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty.');

        $address = $this->createValidAddress();
        new Member(
            'john_doe',
            'john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'John',
            'Michael',
            '',
            Gender::MALE,
            Carbon::parse('1990-05-15'),
            $address
        );
    }

    public function testMiddleNameCanBeEmpty(): void
    {
        $address = $this->createValidAddress();
        $member = new Member(
            'john_doe',
            'john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'John',
            '',
            'Doe',
            Gender::MALE,
            Carbon::parse('1990-05-15'),
            $address
        );

        $this->assertEquals('', $member->getMiddleName());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $member = $this->createValidMember();
        $array = $member->toArray();

        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('first_name', $array);
        $this->assertArrayHasKey('last_name', $array);
        $this->assertArrayHasKey('gender', $array);
        $this->assertArrayHasKey('date_of_birth', $array);
        $this->assertArrayHasKey('address', $array);
        $this->assertEquals('john_doe', $array['username']);
        $this->assertEquals('John', $array['first_name']);
        $this->assertEquals('male', $array['gender']);
    }

    public function testFromArrayCreatesValidMember(): void
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
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'phone_number' => '+9171234567',
            'status' => 'active',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1990-05-15 00:00:00',
            'address' => $addressData,
            'role' => 'member'
        ];

        $member = Member::fromArray($data);

        $this->assertInstanceOf(Member::class, $member);
        $this->assertEquals('john_doe', $member->getUsername());
        $this->assertEquals('John', $member->getFirstName());
    }
}