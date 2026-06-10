<?php

// tests/Unit/Data/Location/CityTest.php

namespace Tests\Unit\Data\Location;

use Tests\TestCase;
use App\Data\Location\City;
use App\Data\Location\Province;
use InvalidArgumentException;

class CityTest extends TestCase
{
    private function createValidProvince(): Province
    {
        return new Province(1, 'Metro Manila');
    }

    public function testCanCreateValidCity(): void
    {
        $province = $this->createValidProvince();
        $city = new City(1, 'Makati City', $province);

        $this->assertEquals(1, $city->getId());
        $this->assertEquals('Makati City', $city->getName());
        $this->assertSame($province, $city->getProvince());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('City ID must be a positive integer.');

        $province = $this->createValidProvince();
        new City(0, 'Makati City', $province);
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('City name cannot be empty.');

        $province = $this->createValidProvince();
        new City(1, '', $province);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $province = $this->createValidProvince();
        $city = new City(1, 'Makati City', $province);
        $array = $city->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('province', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Makati City', $array['name']);
    }

    public function testFromArrayCreatesValidCity(): void
    {
        $provinceData = [
            'id' => 1,
            'name' => 'Metro Manila'
        ];

        $data = [
            'id' => 1,
            'name' => 'Makati City',
            'province' => $provinceData
        ];

        $city = City::fromArray($data);

        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals(1, $city->getId());
        $this->assertEquals('Makati City', $city->getName());
    }
}