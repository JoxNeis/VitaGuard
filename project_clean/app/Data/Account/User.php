<?php

namespace App\Data\Account;

use App\Data\Value\Account\Role;
use App\Data\Value\Account\Status;
use InvalidArgumentException;
use Throwable;

abstract class User
{
    #region FIELDS
    protected string $username;
    protected string $email;
    protected string $phoneNumber;
    protected Role $role;
    protected Status $status;
    #endregion

    #region CONSTRUCTOR
    public function __construct(
        string $username,
        string $email,
        string $phoneNumber,
        Role $role,
        Status $status
    ) {
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setPhoneNumber($phoneNumber);
        $this->setRole($role);
        $this->setStatus($status);
    }
    #endregion

    #region GETTERS
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
    #endregion

    #region SETTERS
    public function setUsername(string $value): void
    {
        $value = trim($value);
        $maxLength = config("data.max_username_length");
        if (empty($value)) {
            throw new InvalidArgumentException("Username cannot be empty.");
        }
        if (mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException("Username cannot exceed {$maxLength} characters.");
        }
        $this->username = $value;
    }

    public function setEmail(string $value): void
    {
        $value = trim($value);
        $maxLength = config("data.max_email_length");
        if (empty($value)) {
            throw new InvalidArgumentException("Email cannot be empty.");
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format.');
        }
        if (mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException("Email cannot exceed {$maxLength} characters.");
        }
        $this->email = $value;
    }

    public function setPhoneNumber(string $value): void
    {
        $value = trim($value);
        $maxLength = config("data.max_phone_number_length");
        $pattern = config("regex.phone_number");
        if (empty($value)) {
            throw new InvalidArgumentException("Phone Number cannot be empty.");
        }
        if (!preg_match($pattern, $value)) {
            throw new InvalidArgumentException('Invalid Phone Number format.');
        }
        if (mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException("Phone Number cannot exceed {$maxLength} characters.");
        }
        $this->phoneNumber = $value;
    }

    public function setRole(Role $value): void
    {
        $this->role = $value;
    }

    public function setStatus(Status $value): void
    {
        $this->status = $value;
    }
    #endregion

    #region UTILITIES
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'role' => $this->role->value,
            'status' => $this->status->value,
        ];
    }
    #endregion
}