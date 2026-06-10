<?php

// tests/Unit/Data/Location/AddressTest.php

namespace Tests\Unit\Data\Location;

use Tests\TestCase;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use InvalidArgumentException;

class AddressTest extends TestCase
{
    private function createValidProvince(): Province
    {
        return new Province(1, 'Metro Manila');
    }

    private function createValidCity(): City
    {
        $province = $this->createValidProvince();
        return new City(1, 'Makati City', $province);
    }

    private function createValidDistrict(): District
    {
        $city = $this->createValidCity();
        return new District(1, 'Poblacion', $city);
    }

    private function createValidAddress(): Address
    {
        $district = $this->createValidDistrict();
        return new Address('123 Test Street', $district);
    }

    public function testCanCreateValidAddress(): void
    {
        $address = $this->createValidAddress();

        $this->assertEquals('123 Test Street', $address->getDetail());
        $this->assertInstanceOf(District::class, $address->getDistrict());
    }

    public function testSetDetailThrowsExceptionForEmptyDetail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Address detail cannot be empty.');

        $district = $this->createValidDistrict();
        new Address('', $district);
    }

    public function testSetDetailThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Address detail cannot be empty.');

        $district = $this->createValidDistrict();
        new Address('   ', $district);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $address = $this->createValidAddress();
        $array = $address->toArray();

        $this->assertArrayHasKey('detail', $array);
        $this->assertArrayHasKey('district', $array);
        $this->assertEquals('123 Test Street', $array['detail']);
    }

    public function testFromArrayCreatesValidAddress(): void
    {
        $provinceData = [
            'id' => 1,
            'name' => 'Metro Manila'
        ];

        $cityData = [
            'id' => 1,
            'name' => 'Makati City',
            'province' => $provinceData
        ];

        $districtData = [
            'id' => 1,
            'name' => 'Poblacion',
            'city' => $cityData
        ];

        $data = [
            'detail' => '123 Test Street',
            'district' => $districtData
        ];

        $address = Address::fromArray($data);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('123 Test Street', $address->getDetail());
        $this->assertEquals('Poblacion', $address->getDistrict()->getName());
    }
}