<?php

// tests/Unit/Data/Service/ConsultationTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\Consultation;
use Carbon\Carbon;
use InvalidArgumentException;

class ConsultationTest extends TestCase
{
    private function createValidConsultation(): Consultation
    {
        return new Consultation(
            1,
            Carbon::parse('2024-01-15 10:00:00'),
            'Patient complained of headache',
            null,
            null
        );
    }

    public function testCanCreateValidConsultation(): void
    {
        $consultation = $this->createValidConsultation();

        $this->assertEquals(1, $consultation->getId());
        $this->assertEquals('2024-01-15 10:00:00', $consultation->getStartTime()->toDateTimeString());
        $this->assertEquals('Patient complained of headache', $consultation->getNotes());
        $this->assertNull($consultation->getEndTime());
        $this->assertNull($consultation->getPaidAt());
        $this->assertFalse($consultation->isClosed());
    }

    public function testSetEndTimeThrowsExceptionWhenEndTimeIsBeforeStartTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End time must be after start time.');

        $consultation = $this->createValidConsultation();
        $consultation->setEndTime(Carbon::parse('2024-01-15 09:00:00'));
    }

    public function testSetEndTimeThrowsExceptionWhenEndTimeEqualsStartTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End time must be after start time.');

        $consultation = $this->createValidConsultation();
        $consultation->setEndTime(Carbon::parse('2024-01-15 10:00:00'));
    }

    public function testSetEndTimeAcceptsValidEndTime(): void
    {
        $consultation = $this->createValidConsultation();
        $end_time = Carbon::parse('2024-01-15 10:30:00');
        $consultation->setEndTime($end_time);

        $this->assertSame($end_time, $consultation->getEndTime());
        $this->assertTrue($consultation->isClosed());
    }

    public function testSetPaidAt(): void
    {
        $consultation = $this->createValidConsultation();
        $paid_at = Carbon::parse('2024-01-15 11:00:00');
        $consultation->setPaidAt($paid_at);

        $this->assertSame($paid_at, $consultation->getPaidAt());
    }

    public function testSetNotes(): void
    {
        $consultation = $this->createValidConsultation();
        $consultation->setNotes('Updated diagnosis notes');

        $this->assertEquals('Updated diagnosis notes', $consultation->getNotes());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $consultation = new Consultation(
            1,
            Carbon::parse('2024-01-15 10:00:00'),
            'Patient notes',
            Carbon::parse('2024-01-15 10:30:00'),
            Carbon::parse('2024-01-15 11:00:00')
        );

        $array = $consultation->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('start_time', $array);
        $this->assertArrayHasKey('end_time', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('paid_at', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Patient notes', $array['notes']);
    }

    public function testFromArrayCreatesValidConsultation(): void
    {
        $data = [
            'id' => '1',
            'start_time' => '2024-01-15 10:00:00',
            'end_time' => '2024-01-15 10:30:00',
            'notes' => 'Patient notes',
            'paid_at' => '2024-01-15 11:00:00'
        ];

        $consultation = Consultation::fromArray($data);

        $this->assertInstanceOf(Consultation::class, $consultation);
        $this->assertEquals(1, $consultation->getId());
        $this->assertEquals('2024-01-15 10:00:00', $consultation->getStartTime()->toDateTimeString());
        $this->assertEquals('2024-01-15 10:30:00', $consultation->getEndTime()->toDateTimeString());
    }

    public function testFromArrayCreatesConsultationWithNullEndTime(): void
    {
        $data = [
            'id' => '1',
            'start_time' => '2024-01-15 10:00:00',
            'end_time' => '',
            'notes' => 'Patient notes',
            'paid_at' => ''
        ];

        $consultation = Consultation::fromArray($data);

        $this->assertNull($consultation->getEndTime());
        $this->assertNull($consultation->getPaidAt());
        $this->assertFalse($consultation->isClosed());
    }
}