<?php

// tests/Unit/Data/Medic/SpecialtyTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\Specialty;
use InvalidArgumentException;

class SpecialtyTest extends TestCase
{
    public function testCanCreateValidSpecialty(): void
    {
        $specialty = new Specialty(1, 'Cardiology');

        $this->assertEquals(1, $specialty->getId());
        $this->assertEquals('Cardiology', $specialty->getName());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Specialty ID must be a positive integer.');

        new Specialty(0, 'Cardiology');
    }

    public function testSetIdThrowsExceptionForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Specialty ID must be a positive integer.');

        new Specialty(-1, 'Cardiology');
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Specialty name cannot be empty.');

        new Specialty(1, '');
    }

    public function testSetNameThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Specialty name cannot be empty.');

        new Specialty(1, '   ');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $specialty = new Specialty(1, 'Cardiology');
        $array = $specialty->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Cardiology', $array['name']);
    }

    public function testFromArrayCreatesValidSpecialty(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Cardiology'
        ];

        $specialty = Specialty::fromArray($data);

        $this->assertInstanceOf(Specialty::class, $specialty);
        $this->assertEquals(1, $specialty->getId());
        $this->assertEquals('Cardiology', $specialty->getName());
    }
}