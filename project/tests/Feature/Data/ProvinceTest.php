<?php

// tests/Unit/Data/Location/ProvinceTest.php

namespace Tests\Unit\Data\Location;

use Tests\TestCase;
use App\Data\Location\Province;
use InvalidArgumentException;

class ProvinceTest extends TestCase
{
    public function testCanCreateValidProvince(): void
    {
        $province = new Province(1, 'Metro Manila');

        $this->assertEquals(1, $province->getId());
        $this->assertEquals('Metro Manila', $province->getName());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Province ID must be a positive integer.');

        new Province(0, 'Metro Manila');
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Province name cannot be empty.');

        new Province(1, '');
    }

    public function testSetNameThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Province name cannot be empty.');

        new Province(1, '   ');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $province = new Province(1, 'Metro Manila');
        $array = $province->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Metro Manila', $array['name']);
    }

    public function testFromArrayCreatesValidProvince(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Metro Manila'
        ];

        $province = Province::fromArray($data);

        $this->assertInstanceOf(Province::class, $province);
        $this->assertEquals(1, $province->getId());
        $this->assertEquals('Metro Manila', $province->getName());
    }
}