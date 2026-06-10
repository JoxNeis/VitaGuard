<?php

// tests/Unit/Data/Service/ChatTest.php

namespace Tests\Unit\Data\Service;

use Tests\TestCase;
use App\Data\Service\Chat;
use App\Data\Account\Member;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;
use App\Data\Location\Address;
use App\Data\Location\District;
use App\Data\Location\City;
use App\Data\Location\Province;
use Carbon\Carbon;
use InvalidArgumentException;

class ChatTest extends TestCase
{
    private function createValidAddress(): Address
    {
        $province = new Province(1, 'Metro Manila');
        $city = new City(1, 'Makati City', $province);
        $district = new District(1, 'Poblacion', $city);
        return new Address('123 Test Street', $district);
    }

    private function createValidMember(): Member
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


    public function testCanCreateValidChatWithSender(): void
    {
        $sender = $this->createValidMember();
        $chat = new Chat(1, 'Hello, world!', $sender);

        $this->assertEquals(1, $chat->getId());
        $this->assertEquals('Hello, world!', $chat->getMessage());
        $this->assertSame($sender, $chat->getSender());
    }

    public function testSetMessageThrowsExceptionForEmptyMessage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Chat message cannot be empty.');

        $sender = $this->createValidMember();
        $chat = new Chat(1, '', $sender);
    }

    public function testSetMessageThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Chat message cannot be empty.');

        $sender = $this->createValidMember();
        new Chat(1, '   ',$sender);
    }

    public function testSetMessageAcceptsValidMessage(): void
    {
        $sender = $this->createValidMember();
        $chat = new Chat(1, 'Hello, world!', $sender);
        $chat->setMessage('Updated message');
        $this->assertEquals('Updated message', $chat->getMessage());
    }

    public function testSetSender(): void
    {
        $sender = $this->createValidMember();
        $chat = new Chat(1, 'Hello, world!', $sender);
        $sender = $this->createValidMember();
        $chat->setSender($sender);

        $this->assertSame($sender, $chat->getSender());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $sender = $this->createValidMember();
        $chat = new Chat(1, 'Hello, world!', $sender);

        $array = $chat->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertArrayHasKey('sender', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Hello, world!', $array['message']);
        $this->assertIsArray($array['sender']);
    }

    public function testToArrayReturnsNullSenderWhenNoSender(): void
    {
        $sender = $this->createValidMember();
        $chat = new Chat(1, 'Hello, world!', $sender);
        $array = $chat->toArray();

        $this->assertNotNull($array['sender']);
    }


    public function testFromArrayCreatesValidChatWithSender(): void
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

        $senderData = [
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

        $data = [
            'id' => '1',
            'message' => 'Hello, world!',
            'sender' => $senderData
        ];

        $chat = Chat::fromArray($data);

        $this->assertInstanceOf(Chat::class, $chat);
        $this->assertEquals(1, $chat->getId());
        $this->assertNotNull($chat->getSender());
    }
}