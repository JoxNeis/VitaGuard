<?php

// tests/Unit/Data/Medic/PrescriptionDetailTest.php

namespace Tests\Unit\Data\Medic;

use Tests\TestCase;
use App\Data\Medic\PrescriptionDetail;
use App\Data\Medic\Medicine;
use App\Data\Service\Facility;
use App\Data\Value\Medic\DosageForm;
use App\Data\Value\Medic\MedicineClass;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

class PrescriptionDetailTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    private function createValidMedicine(): Medicine
    {
        return new Medicine(
            1,
            'Paracetamol',
            DosageForm::TABLET,
            MedicineClass::OTC,
            'For fever and pain'
        );
    }

    private function createValidFacility(): Facility
    {
        $address = $this->createValidAddress();
        return new Facility(1, 'Test Hospital', $address, '+9171234567', 4.5);
    }

    private function createValidPrescriptionDetail(): PrescriptionDetail
    {
        $medicine = $this->createValidMedicine();
        $facility = $this->createValidFacility();

        return new PrescriptionDetail(
            1,
            $medicine,
            30,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-02-01'),
            Carbon::parse('2024-01-01 10:00:00'),
            $facility,
            'Take one tablet twice daily'
        );
    }

    public function testCanCreateValidPrescriptionDetail(): void
    {
        $detail = $this->createValidPrescriptionDetail();

        $this->assertEquals(1, $detail->getId());
        $this->assertInstanceOf(Medicine::class, $detail->getMedicine());
        $this->assertEquals(30, $detail->getQuantity());
        $this->assertEquals('2024-01-01', $detail->getStart()->toDateString());
        $this->assertEquals('2024-02-01', $detail->getEnd()->toDateString());
        $this->assertEquals('2024-01-01 10:00:00', $detail->getTaken()->toDateTimeString());
        $this->assertInstanceOf(Facility::class, $detail->getTakenAt());
        $this->assertEquals('Take one tablet twice daily', $detail->getInstructions());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be a positive integer.');

        $medicine = $this->createValidMedicine();
        $facility = $this->createValidFacility();

        new PrescriptionDetail(
            0,
            $medicine,
            30,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-02-01'),
            Carbon::parse('2024-01-01 10:00:00'),
            $facility,
            'Instructions'
        );
    }

    public function testSetQuantityThrowsExceptionForNegativeQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity cannot be negative.');

        $medicine = $this->createValidMedicine();
        $facility = $this->createValidFacility();

        new PrescriptionDetail(
            1,
            $medicine,
            -1,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-02-01'),
            Carbon::parse('2024-01-01 10:00:00'),
            $facility,
            'Instructions'
        );
    }

    public function testSetQuantityAcceptsZero(): void
    {
        $medicine = $this->createValidMedicine();
        $facility = $this->createValidFacility();

        $detail = new PrescriptionDetail(
            1,
            $medicine,
            0,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-02-01'),
            Carbon::parse('2024-01-01 10:00:00'),
            $facility,
            'Instructions'
        );

        $this->assertEquals(0, $detail->getQuantity());
    }

    public function testSetEndThrowsExceptionWhenEndIsBeforeStart(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End date cannot be before the start date.');

        $medicine = $this->createValidMedicine();
        $facility = $this->createValidFacility();

        new PrescriptionDetail(
            1,
            $medicine,
            30,
            Carbon::parse('2024-02-01'),
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-01-01 10:00:00'),
            $facility,
            'Instructions'
        );
    }

    public function testSetInstructionsThrowsExceptionForEmptyInstructions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Instructions cannot be empty.');

        $medicine = $this->createValidMedicine();
        $facility = $this->createValidFacility();

        new PrescriptionDetail(
            1,
            $medicine,
            30,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-02-01'),
            Carbon::parse('2024-01-01 10:00:00'),
            $facility,
            ''
        );
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $detail = $this->createValidPrescriptionDetail();
        $array = $detail->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('medicine', $array);
        $this->assertArrayHasKey('quantity', $array);
        $this->assertArrayHasKey('start', $array);
        $this->assertArrayHasKey('end', $array);
        $this->assertArrayHasKey('taken', $array);
        $this->assertArrayHasKey('takenAt', $array);
        $this->assertArrayHasKey('instructions', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals(30, $array['quantity']);
    }

    public function testFromArrayCreatesValidPrescriptionDetail(): void
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

        $medicineData = [
            'id' => 1,
            'name' => 'Paracetamol',
            'dosage_form' => 'tablet',
            'medicine_class' => 'over_the_counter',
            'description' => 'For fever and pain'
        ];

        $facilityData = [
            'id' => 1,
            'name' => 'Test Hospital',
            'address' => $addressData,
            'phone_number' => '+9171234567',
            'rating' => 4.5
        ];

        $data = [
            'id' => 1,
            'medicine' => $medicineData,
            'quantity' => 30,
            'start' => '2024-01-01',
            'end' => '2024-02-01',
            'taken' => '2024-01-01 10:00:00',
            'takenAt' => $facilityData,
            'instructions' => 'Take one tablet twice daily'
        ];

        $detail = PrescriptionDetail::fromArray($data);

        $this->assertInstanceOf(PrescriptionDetail::class, $detail);
        $this->assertEquals(1, $detail->getId());
        $this->assertEquals('Paracetamol', $detail->getMedicine()->getName());
    }
}