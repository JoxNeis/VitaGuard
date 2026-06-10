<?php

// tests/Unit/Data/Medic/MemberAllergenTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\MemberAllergen;
use App\Data\Medic\Allergen;
use App\Data\Value\Medic\AllergenSeverity;
use App\Data\Account\Member;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

class MemberAllergenTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    private function createValidAllergen(): Allergen
    {
        return new Allergen(1, 'Peanuts');
    }

    private function createValidCreator(): Member
    {
        $address = $this->createValidAddress();

        return new Member(
            'doctor_user',
            'doctor@example.com',
            '+9171234567',
            Status::ACTIVE,
            'John',
            '',
            'Doe',
            Gender::MALE,
            Carbon::parse('1980-01-01'),
            $address
        );
    }

    private function createValidMemberAllergen(): MemberAllergen
    {
        $allergen = $this->createValidAllergen();
        $creator = $this->createValidCreator();

        return new MemberAllergen(
            1,
            $allergen,
            'Causes hives and difficulty breathing',
            AllergenSeverity::SEVERE,
            'Patient should carry epinephrine',
            $creator
        );
    }

    public function testCanCreateValidMemberAllergen(): void
    {
        $memberAllergen = $this->createValidMemberAllergen();

        $this->assertEquals(1, $memberAllergen->getId());
        $this->assertInstanceOf(Allergen::class, $memberAllergen->getAllergen());
        $this->assertEquals('Causes hives and difficulty breathing', $memberAllergen->getDescription());
        $this->assertEquals(AllergenSeverity::SEVERE, $memberAllergen->getSeverity());
        $this->assertEquals('Patient should carry epinephrine', $memberAllergen->getNotes());
        $this->assertInstanceOf(Member::class, $memberAllergen->getCreator());
    }

    public function testSetDescriptionThrowsExceptionForEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Description cannot be empty.');

        $allergen = $this->createValidAllergen();
        $creator = $this->createValidCreator();

        new MemberAllergen(1, $allergen, '', AllergenSeverity::MILD, '', $creator);
    }

    public function testSetDescriptionThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Description cannot be empty.');

        $allergen = $this->createValidAllergen();
        $creator = $this->createValidCreator();

        new MemberAllergen(1, $allergen, '   ', AllergenSeverity::MILD, '', $creator);
    }

    public function testNotesCanBeEmpty(): void
    {
        $allergen = $this->createValidAllergen();
        $creator = $this->createValidCreator();

        $memberAllergen = new MemberAllergen(
            1,
            $allergen,
            'Causes reaction',
            AllergenSeverity::MODERATE,
            '',
            $creator
        );

        $this->assertEquals('', $memberAllergen->getNotes());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $memberAllergen = $this->createValidMemberAllergen();
        $array = $memberAllergen->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('allergen', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('severity', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('creator', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('severe', $array['severity']);
    }

    public function testFromArrayCreatesValidMemberAllergen(): void
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

        $allergenData = [
            'id' => 1,
            'name' => 'Peanuts'
        ];

        $creatorData = [
            'username' => 'doctor_user',
            'email' => 'doctor@example.com',
            'phone_number' => '+9171234567',
            'role' => 'member',
            'status' => 'active',
            'first_name' => 'John',
            'middle_name' => '',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1980-01-01 00:00:00',
            'address' => $addressData
        ];

        $data = [
            'id' => 1,
            'allergen' => $allergenData,
            'description' => 'Causes hives',
            'severity' => 'severe',
            'notes' => 'Carry epinephrine',
            'creator' => $creatorData
        ];

        $memberAllergen = MemberAllergen::fromArray($data);

        $this->assertInstanceOf(MemberAllergen::class, $memberAllergen);
        $this->assertEquals(1, $memberAllergen->getId());
        $this->assertEquals('Peanuts', $memberAllergen->getAllergen()->getName());
    }
}