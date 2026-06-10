<?php

// tests/Unit/Data/Service/FacilityTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\Facility;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use InvalidArgumentException;

class FacilityTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    public function testCanCreateValidFacility(): void
    {
        $address = $this->createValidAddress();
        $facility = new Facility(1, 'Test Hospital', $address, '+9171234567', 4.5);

        $this->assertEquals(1, $facility->getId());
        $this->assertEquals('Test Hospital', $facility->getName());
        $this->assertSame($address, $facility->getAddress());
        $this->assertEquals('+9171234567', $facility->getPhoneNumber());
        $this->assertEquals(4.5, $facility->getRating());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Facility ID must be a positive integer.');

        $address = $this->createValidAddress();
        new Facility(0, 'Test Hospital', $address, '+9171234567', 4.5);
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Facility Name cannot be empty.');

        $address = $this->createValidAddress();
        new Facility(1, '', $address, '+9171234567', 4.5);
    }

    public function testSetNameThrowsExceptionForWhitespaceOnlyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Facility Name cannot be empty.');

        $address = $this->createValidAddress();
        new Facility(1, '   ', $address, '+9171234567', 4.5);
    }

    public function testSetRatingThrowsExceptionForOutOfRangeRating(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rating must be between 0.0 and 5.0.');

        $address = $this->createValidAddress();
        new Facility(1, 'Test Hospital', $address, '+9171234567', 5.5);
    }

    public function testSetRatingAcceptsMinimumValue(): void
    {
        $address = $this->createValidAddress();
        $facility = new Facility(1, 'Test Hospital', $address, '+9171234567', 0.0);

        $this->assertEquals(0.0, $facility->getRating());
    }

    public function testSetRatingAcceptsMaximumValue(): void
    {
        $address = $this->createValidAddress();
        $facility = new Facility(1, 'Test Hospital', $address, '+9171234567', 5.0);

        $this->assertEquals(5.0, $facility->getRating());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $address = $this->createValidAddress();
        $facility = new Facility(1, 'Test Hospital', $address, '+9171234567', 4.5);

        $array = $facility->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('address', $array);
        $this->assertArrayHasKey('phone_number', $array);
        $this->assertArrayHasKey('rating', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Test Hospital', $array['name']);
        $this->assertEquals('+9171234567', $array['phone_number']);
        $this->assertEquals(4.5, $array['rating']);
    }

    public function testFromArrayCreatesValidFacility(): void
    {
        $addressData = [
            'detail' => '123 Test Street',
            'district' => [
                'id' => 1,
                'name' => 'Poblacion',
                'city' => [
                    'id' => 1,
                    'name' => 'Makati City',
                    'province' => [
                        'id' => 1,
                        'name' => 'Metro Manila'
                    ]
                ]
            ]
        ];

        $data = [
            'id' => '1',
            'name' => 'Test Hospital',
            'address' => $addressData,
            'phone_number' => '+9171234567',
            'rating' => '4.5'
        ];

        $facility = Facility::fromArray($data);

        $this->assertInstanceOf(Facility::class, $facility);
        $this->assertEquals(1, $facility->getId());
        $this->assertEquals('Test Hospital', $facility->getName());
    }
}