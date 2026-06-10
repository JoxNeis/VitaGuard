<?php

// tests/Unit/Data/Account/AdminTest.php

namespace Tests\Unit\Data\Account;

use Tests\TestCase;
use App\Data\Account\Admin;
use App\Data\Value\Account\Status;
use InvalidArgumentException;

class AdminTest extends TestCase
{
    private function createValidAdmin(): Admin
    {
        return new Admin(
            'super_admin',
            'admin@system.com',
            '+9171234567',
            Status::ACTIVE
        );
    }

    public function testCanCreateValidAdmin(): void
    {
        $admin = $this->createValidAdmin();

        $this->assertEquals('super_admin', $admin->getUsername());
        $this->assertEquals('admin@system.com', $admin->getEmail());
        $this->assertEquals('+9171234567', $admin->getPhoneNumber());
        $this->assertEquals(Status::ACTIVE, $admin->getStatus());
    }

    public function testAdminHasCorrectRole(): void
    {
        $admin = $this->createValidAdmin();
        
        $this->assertEquals('admin', $admin->getRole()->value);
    }

    public function testAdminCanBeCreatedWithDefaultStatus(): void
    {
        $admin = new Admin('super_admin', 'admin@system.com', '+9171234567');
        
        $this->assertEquals(Status::ACTIVE, $admin->getStatus());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $admin = $this->createValidAdmin();
        $array = $admin->toArray();

        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('phone_number', $array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertEquals('super_admin', $array['username']);
        $this->assertEquals('admin', $array['role']);
        $this->assertEquals('active', $array['status']);
    }

    public function testFromArrayCreatesValidAdmin(): void
    {
        $data = [
            'username' => 'super_admin',
            'email' => 'admin@system.com',
            'phone_number' => '+9171234567',
            'status' => 'active',
            'role' => 'admin'
        ];

        $admin = Admin::fromArray($data);

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertEquals('super_admin', $admin->getUsername());
        $this->assertEquals('admin@system.com', $admin->getEmail());
    }

    public function testFromArrayThrowsExceptionForMissingKeys(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $data = [
            'username' => 'super_admin',
            'email' => 'admin@system.com'
        ];

        Admin::fromArray($data);
    }
}