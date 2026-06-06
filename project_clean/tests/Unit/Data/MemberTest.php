<?php

namespace Tests\Unit\Data\Account;

use Tests\TestCase;
use Carbon\Carbon;

use App\Data\Account\Member;
use App\Data\Location\Province;
use App\Data\Location\City;
use App\Data\Location\District;
use App\Data\Location\Address;

use App\Data\Value\Account\Gender;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Role;

use InvalidArgumentException;

class MemberTest extends TestCase
{
    private function createAddress(): Address
    {
        $prov = new Province(1, "East Java");
        $city = new City(1, "Surabaya", $prov);
        $dist = new District(1, "Gubeng", $city);
        $address = new Address("Jalan Raya Gubeng No.1", $dist);
        return $address;

    }

    private function createMember(): Member
    {
        return new Member(
            username: 'joshua',
            email: 'joshua@example.com',
            phoneNumber: '+628123456789',
            status: Status::ACTIVE,
            firstName: 'Joshua',
            middleName: 'Nehemia',
            lastName: 'Tan',
            gender: Gender::MALE,
            dateOfBirth: Carbon::parse('2000-01-01'),
            address: $this->createAddress()
        );
    }

    public function test_constructor_sets_all_properties(): void
    {
        $member = $this->createMember();

        $this->assertEquals('joshua', $member->getUsername());
        $this->assertEquals('joshua@example.com', $member->getEmail());
        $this->assertEquals('+628123456789', $member->getPhoneNumber());

        $this->assertEquals(Role::MEMBER, $member->getRole());
        $this->assertEquals(Status::ACTIVE, $member->getStatus());

        $this->assertEquals('Joshua', $member->getFirstName());
        $this->assertEquals('Nehemia', $member->getMiddleName());
        $this->assertEquals('Tan', $member->getLastName());

        $this->assertEquals(Gender::MALE, $member->getGender());
        $this->assertEquals(
            '2000-01-01',
            $member->getDateOfBirth()->toDateString()
        );

        $this->assertInstanceOf(Address::class, $member->getAddress());
    }

    public function test_first_name_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty.');

        $member = $this->createMember();
        $member->setFirstName('');
    }

    public function test_middle_name_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Middle name cannot be empty.');

        $member = $this->createMember();
        $member->setMiddleName('');
    }

    public function test_last_name_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty.');

        $member = $this->createMember();
        $member->setLastName('');
    }

    public function test_to_array_returns_expected_structure(): void
    {
        $member = $this->createMember();

        $array = $member->toArray();

        $this->assertEquals('joshua', $array['username']);
        $this->assertEquals('Joshua', $array['firstName']);
        $this->assertEquals('Nehemia', $array['middleName']);
        $this->assertEquals('Tan', $array['lastName']);

        $this->assertEquals(
            Gender::MALE->value,
            $array['gender']
        );

        $this->assertArrayHasKey('address', $array);
    }

    public function test_from_array_creates_member(): void
    {
        $member = $this->createMember();

        $reconstructed = Member::fromArray(
            $member->toArray()
        );

        $this->assertEquals(
            $member->getUsername(),
            $reconstructed->getUsername()
        );

        $this->assertEquals(
            $member->getEmail(),
            $reconstructed->getEmail()
        );

        $this->assertEquals(
            $member->getFirstName(),
            $reconstructed->getFirstName()
        );

        $this->assertEquals(
            $member->getGender(),
            $reconstructed->getGender()
        );
    }

    public function test_from_array_requires_first_name(): void
    {
        $data = $this->createMember()->toArray();

        unset($data['firstName']);

        $this->expectException(\InvalidArgumentException::class);

        Member::fromArray($data);
    }
}