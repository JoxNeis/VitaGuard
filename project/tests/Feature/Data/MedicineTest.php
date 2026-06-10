<?php

// tests/Unit/Data/Medic/MedicineTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\Medicine;
use App\Data\Value\Medic\DosageForm;
use App\Data\Value\Medic\MedicineClass;
use InvalidArgumentException;

class MedicineTest extends TestCase
{
    public function testCanCreateValidMedicine(): void
    {
        $medicine = new Medicine(
            1,
            'Paracetamol',
            DosageForm::TABLET,
            MedicineClass::OTC,
            'For fever and pain relief'
        );

        $this->assertEquals(1, $medicine->getId());
        $this->assertEquals('Paracetamol', $medicine->getName());
        $this->assertEquals(DosageForm::TABLET, $medicine->getDosageForm());
        $this->assertEquals(MedicineClass::OTC, $medicine->getMedicineClass());
        $this->assertEquals('For fever and pain relief', $medicine->getDescription());
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Medicine name cannot be empty.');

        new Medicine(1, '', DosageForm::TABLET, MedicineClass::OTC, 'Description');
    }

    public function testSetNameThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Medicine name cannot be empty.');

        new Medicine(1, '   ', DosageForm::TABLET, MedicineClass::OTC, 'Description');
    }

    public function testSetDescriptionThrowsExceptionForEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Medicine description cannot be empty.');

        new Medicine(1, 'Paracetamol', DosageForm::TABLET, MedicineClass::OTC, '');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $medicine = new Medicine(
            1,
            'Paracetamol',
            DosageForm::TABLET,
            MedicineClass::OTC,
            'Description'
        );

        $array = $medicine->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('dosage_form', $array);
        $this->assertArrayHasKey('medicine_class', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Paracetamol', $array['name']);
        $this->assertEquals('tablet', $array['dosage_form']);
        $this->assertEquals('over_the_counter', $array['medicine_class']);
    }

    public function testFromArrayCreatesValidMedicine(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Paracetamol',
            'dosage_form' => 'tablet',
            'medicine_class' => 'over_the_counter',
            'description' => 'For fever and pain'
        ];

        $medicine = Medicine::fromArray($data);

        $this->assertInstanceOf(Medicine::class, $medicine);
        $this->assertEquals(1, $medicine->getId());
        $this->assertEquals('Paracetamol', $medicine->getName());
    }
}