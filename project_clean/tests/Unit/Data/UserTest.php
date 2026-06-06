<?php

namespace Tests\Unit\Data;

use App\Data\Account\User;
use App\Data\Value\Account\Role;
use App\Data\Value\Account\Status;
use InvalidArgumentException;
use Tests\TestCase;

class TestUser extends User
{
}

class UserTest extends TestCase
{
    public function test_can_create_user(): void
    {
        $user = new TestUser(
            'joshua',
            'joshua@example.com',
            '+6281234567890',
            Role::MEMBER,
            Status::ACTIVE
        );

        $this->assertEquals('joshua', $user->getUsername());
        $this->assertEquals('joshua@example.com', $user->getEmail());
        $this->assertEquals('+6281234567890', $user->getPhoneNumber());
        $this->assertEquals(Role::MEMBER, $user->getRole());
        $this->assertEquals(Status::ACTIVE, $user->getStatus());
    }

    public function test_username_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty.');

        new TestUser(
            '',
            'joshua@example.com',
            '+6281234567890',
            Role::MEMBER,
            Status::ACTIVE
        );
    }

    public function test_email_must_be_valid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format.');

        new TestUser(
            'joshua',
            'invalid-email',
            '+6281234567890',
            Role::MEMBER,
            Status::ACTIVE
        );
    }

    public function test_phone_number_must_be_valid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TestUser(
            'joshua',
            'joshua@example.com',
            'abc123',
            Role::MEMBER,
            Status::ACTIVE
        );
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $user = new TestUser(
            'joshua',
            'joshua@example.com',
            '+6281234567890',
            Role::MEMBER,
            Status::ACTIVE
        );

        $this->assertEquals([
            'username' => 'joshua',
            'email' => 'joshua@example.com',
            'phoneNumber' => '+6281234567890',
            'role' => Role::MEMBER->value,
            'status' => Status::ACTIVE->value
        ], $user->toArray());
    }
}