<?php

// tests/Unit/Data/Location/DistrictTest.php

namespace Tests\Unit\Data\Location;

use Tests\TestCase;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use InvalidArgumentException;

class DistrictTest extends TestCase
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

    public function testCanCreateValidDistrict(): void
    {
        $city = $this->createValidCity();
        $district = new District(1, 'Poblacion', $city);

        $this->assertEquals(1, $district->getId());
        $this->assertEquals('Poblacion', $district->getName());
        $this->assertSame($city, $district->getCity());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('District ID must be a positive integer.');

        $city = $this->createValidCity();
        new District(0, 'Poblacion', $city);
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('District name cannot be empty.');

        $city = $this->createValidCity();
        new District(1, '', $city);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $city = $this->createValidCity();
        $district = new District(1, 'Poblacion', $city);
        $array = $district->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('city', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Poblacion', $array['name']);
    }

    public function testFromArrayCreatesValidDistrict(): void
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

        $data = [
            'id' => 1,
            'name' => 'Poblacion',
            'city' => $cityData
        ];

        $district = District::fromArray($data);

        $this->assertInstanceOf(District::class, $district);
        $this->assertEquals(1, $district->getId());
        $this->assertEquals('Poblacion', $district->getName());
    }
}