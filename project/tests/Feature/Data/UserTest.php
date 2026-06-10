<?php

// tests/Unit/Data/Account/UserTest.php

namespace Tests\Unit\Data\Account;

use Tests\TestCase;
use App\Data\Account\User;
use App\Data\Account\Member;
use App\Data\Account\Doctor;
use App\Data\Account\Admin;
use App\Data\Account\FacilityAdmin;
use App\Data\Value\Account\Role;
use App\Data\Value\Account\Status;
use InvalidArgumentException;
use RuntimeException;

// Concrete implementation for testing abstract class
class TestUser extends User
{
    public static function fromArray(array $data): self
    {
        return new self(
            $data['username'],
            $data['email'],
            $data['phone_number'],
            Role::from($data['role']),
            Status::from($data['status'])
        );
    }
}

class UserTest extends TestCase
{
    private function createTestUser(): TestUser
    {
        return new TestUser(
            'testuser',
            'test@example.com',
            '+9171234567',
            Role::MEMBER,
            Status::ACTIVE
        );
    }

    public function testCanCreateValidUser(): void
    {
        $user = $this->createTestUser();

        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('+9171234567', $user->getPhoneNumber());
        $this->assertEquals(Role::MEMBER, $user->getRole());
        $this->assertEquals(Status::ACTIVE, $user->getStatus());
    }

    public function testSetUsernameThrowsExceptionForEmptyUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty.');

        new TestUser('', 'test@example.com', '+9171234567', Role::MEMBER, Status::ACTIVE);
    }

    public function testSetUsernameThrowsExceptionForWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty.');

        new TestUser('   ', 'test@example.com', '+9171234567', Role::MEMBER, Status::ACTIVE);
    }

    public function testSetEmailThrowsExceptionForInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format.');

        new TestUser('testuser', 'invalid-email', '+9171234567', Role::MEMBER, Status::ACTIVE);
    }

    public function testSetEmailThrowsExceptionForEmptyEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty.');

        new TestUser('testuser', '', '+9171234567', Role::MEMBER, Status::ACTIVE);
    }

    public function testSetEmailAcceptsValidEmail(): void
    {
        $user = new TestUser('testuser', 'user.name+tag@example.co.uk', '+9171234567', Role::MEMBER, Status::ACTIVE);

        $this->assertEquals('user.name+tag@example.co.uk', $user->getEmail());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $user = $this->createTestUser();

        $array = $user->toArray();

        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('phone_number', $array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertEquals('testuser', $array['username']);
        $this->assertEquals('member', $array['role']);
        $this->assertEquals('active', $array['status']);
    }
    
    public function testFromArrayCreatesCorrectChildClass(): void
    {
        // This test verifies the dynamic class loading in the parent User::fromArray
        // We'll test this through concrete child classes
        $memberData = [
            'username' => 'member1',
            'email' => 'member@example.com',
            'phone_number' => '+9171234567',
            'role' => 'member',
            'status' => 'active',
            'first_name' => 'John',
            'middle_name' => '',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01 00:00:00',
            'address' => [
                'detail' => '123 Test St',
                'district' => [
                    'id' => 1,
                    'name' => 'District1',
                    'city' => [
                        'id' => 1,
                        'name' => 'City1',
                        'province' => ['id' => 1, 'name' => 'Province1']
                    ]
                ]
            ]
        ];

        $member = Member::fromArray($memberData);
        $this->assertInstanceOf(Member::class, $member);
    }
}