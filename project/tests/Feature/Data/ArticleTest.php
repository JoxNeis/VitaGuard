<?php

// tests/Unit/Data/Content/ArticleTest.php

namespace Tests\Unit\Data\Content;

use Tests\TestCase;
use App\Data\Content\Article;
use App\Data\Content\Topic;
use App\Data\Account\Member;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

use function PHPUnit\Framework\assertSame;

class ArticleTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    private function createValidCreator(): Member
    {
        $address = $this->createValidAddress();

        return new Member(
            'john_doe',
            'john@example.com',
            '+9171234567',
            Status::ACTIVE,
            'John',
            '',
            'Doe',
            Gender::MALE,
            Carbon::parse('1990-01-01'),
            $address
        );
    }

    private function createValidTopic(): Topic
    {
        return new Topic(1, 'Health & Wellness');
    }

    private function createValidArticle(): Article
    {
        $creator = $this->createValidCreator();
        $topic = $this->createValidTopic();

        return new Article(
            $creator,
            $topic,
            'This is the article content...',
            Carbon::parse('2024-01-01 10:00:00'),
            Carbon::parse('2024-01-01 10:00:00')
        );
    }

    public function testCanCreateValidArticle(): void
    {
        $article = $this->createValidArticle();

        $this->assertInstanceOf(Member::class, $article->getCreator());
        $this->assertInstanceOf(Topic::class, $article->getTopic());
        $this->assertEquals('This is the article content...', $article->getContent());
        $this->assertInstanceOf(Carbon::class, $article->getCreatedAt());
        $this->assertInstanceOf(Carbon::class, $article->getUpdatedAt());
    }

    public function testCanCreateArticleWithAutoTimestamps(): void
    {
        $creator = $this->createValidCreator();
        $topic = $this->createValidTopic();

        $article = new Article($creator, $topic, 'Content');

        $this->assertNotNull($article->getCreatedAt());
        $this->assertNotNull($article->getUpdatedAt());
    }

    public function testSetContentThrowsExceptionForEmptyContent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Article content cannot be empty.');

        $creator = $this->createValidCreator();
        $topic = $this->createValidTopic();

        new Article($creator, $topic, '');
    }

    public function testSetContentThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Article content cannot be empty.');

        $creator = $this->createValidCreator();
        $topic = $this->createValidTopic();

        new Article($creator, $topic, '   ');
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $article = $this->createValidArticle();
        $array = $article->toArray();

        $this->assertArrayHasKey('creator', $array);
        $this->assertArrayHasKey('topic', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertEquals('This is the article content...', $array['content']);
    }

    public function testFromArrayCreatesValidArticle(): void
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

        $creatorData = [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'phone_number' => '+9171234567',
            'role' => 'member',
            'status' => 'active',
            'first_name' => 'John',
            'middle_name' => '',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01 00:00:00',
            'address' => $addressData
        ];

        $topicData = [
            'id' => 1,
            'name' => 'Health & Wellness'
        ];

        $data = [
            'creator' => $creatorData,
            'topic' => $topicData,
            'content' => 'Article content here',
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-01 10:00:00'
        ];
        $article = Article::fromArray($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Article content here', $article->getContent());
        $this->assertEquals('Health & Wellness', $article->getTopic()->getName());
    }
}