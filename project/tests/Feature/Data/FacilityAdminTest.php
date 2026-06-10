<?php

// tests/Unit/Data/Account/FacilityAdminTest.php

namespace Tests\Unit\Data\Account;

use Tests\TestCase;
use App\Data\Account\FacilityAdmin;
use App\Data\Service\Facility;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use App\Data\Value\Account\Status;
use InvalidArgumentException;

class FacilityAdminTest extends TestCase
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

    private function createValidFacilityAdmin(): FacilityAdmin
    {
        $facility = $this->createValidFacility();

        return new FacilityAdmin(
            'admin_hospital',
            'admin@hospital.com',
            '+9171234567',
            $facility,
            Status::ACTIVE
        );
    }

    public function testCanCreateValidFacilityAdmin(): void
    {
        $admin = $this->createValidFacilityAdmin();

        $this->assertEquals('admin_hospital', $admin->getUsername());
        $this->assertEquals('admin@hospital.com', $admin->getEmail());
        $this->assertEquals('+9171234567', $admin->getPhoneNumber());
        $this->assertInstanceOf(Facility::class, $admin->getFacility());
        $this->assertEquals(Status::ACTIVE, $admin->getStatus());
    }

    public function testSetFacility(): void
    {
        $admin = $this->createValidFacilityAdmin();
        $newFacility = $this->createValidFacility();
        
        $admin->setFacility($newFacility);
        
        $this->assertSame($newFacility, $admin->getFacility());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $admin = $this->createValidFacilityAdmin();
        $array = $admin->toArray();

        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('phone_number', $array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('facility', $array);
        $this->assertEquals('admin_hospital', $array['username']);
        $this->assertEquals('facility_admin', $array['role']);
    }

    public function testFromArrayCreatesValidFacilityAdmin(): void
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
            'username' => 'admin_hospital',
            'email' => 'admin@hospital.com',
            'phone_number' => '+9171234567',
            'facility' => $facilityData,
            'status' => 'active',
            'role' => 'facility_admin'
        ];

        $admin = FacilityAdmin::fromArray($data);

        $this->assertInstanceOf(FacilityAdmin::class, $admin);
        $this->assertEquals('admin_hospital', $admin->getUsername());
        $this->assertEquals('Test Hospital', $admin->getFacility()->getName());
    }
}