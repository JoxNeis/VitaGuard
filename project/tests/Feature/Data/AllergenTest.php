<?php

// tests/Unit/Data/Medic/AllergenTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\Allergen;
use InvalidArgumentException;

class AllergenTest extends TestCase
{
    public function testCanCreateValidAllergen(): void
    {
        $allergen = new Allergen(1, 'Peanuts');

        $this->assertEquals(1, $allergen->getId());
        $this->assertEquals('Peanuts', $allergen->getName());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic ID must be a positive integer.');

        new Allergen(0, 'Peanuts');
    }

    public function testSetIdThrowsExceptionForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic ID must be a positive integer.');

        new Allergen(-1, 'Peanuts');
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic name cannot be empty.');

        new Allergen(1, '');
    }

    public function testSetNameThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic name cannot be empty.');

        new Allergen(1, '   ');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $allergen = new Allergen(1, 'Peanuts');
        $array = $allergen->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Peanuts', $array['name']);
    }

    public function testFromArrayCreatesValidAllergen(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Peanuts'
        ];

        $allergen = Allergen::fromArray($data);

        $this->assertInstanceOf(Allergen::class, $allergen);
        $this->assertEquals(1, $allergen->getId());
        $this->assertEquals('Peanuts', $allergen->getName());
    }
}