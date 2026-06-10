<?php

// tests/Unit/Data/Content/TopicTest.php

namespace Tests\Unit\Data\Content;

use Tests\TestCase;
use App\Data\Content\Topic;
use InvalidArgumentException;

class TopicTest extends TestCase
{
    public function testCanCreateValidTopic(): void
    {
        $topic = new Topic(1, 'Health & Wellness');

        $this->assertEquals(1, $topic->getId());
        $this->assertEquals('Health & Wellness', $topic->getName());
    }

    public function testSetIdThrowsExceptionForNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic ID must be a positive integer.');

        new Topic(0, 'Health');
    }

    public function testSetIdThrowsExceptionForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic ID must be a positive integer.');

        new Topic(-1, 'Health');
    }

    public function testSetNameThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic name cannot be empty.');

        new Topic(1, '');
    }

    public function testSetNameThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Topic name cannot be empty.');

        new Topic(1, '   ');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $topic = new Topic(1, 'Health & Wellness');
        $array = $topic->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Health & Wellness', $array['name']);
    }

    public function testFromArrayCreatesValidTopic(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Health & Wellness'
        ];

        $topic = Topic::fromArray($data);

        $this->assertInstanceOf(Topic::class, $topic);
        $this->assertEquals(1, $topic->getId());
        $this->assertEquals('Health & Wellness', $topic->getName());
    }
}