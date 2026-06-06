<?php

namespace App\Data\Account;

use App\Data\Location\Address;

use App\Data\Value\Account\Role;
use App\Data\Value\Account\Status;
use App\Data\Value\Account\Gender;

use Carbon\Carbon;
use InvalidArgumentException;

class Member extends User
{
    #region PROPERTIES
    private string $firstName;
    private string $middleName;
    private string $lastName;
    private Gender $gender;
    private Carbon $dateOfBirth;
    private Address $address;
    #endregion

    #region CONSTRUCTOR
    public function __construct(
        string $username,
        string $email,
        string $phoneNumber,
        Status $status,
        string $firstName,
        string $middleName,
        string $lastName,
        Gender $gender,
        Carbon $dateOfBirth,
        Address $address
    ) {
        parent::__construct($username, $email, $phoneNumber, Role::MEMBER, $status);
        $this->setFirstName($firstName);
        $this->setMiddleName($middleName);
        $this->setLastName($lastName);
        $this->setGender($gender);
        $this->setDateOfBirth($dateOfBirth);
        $this->setAddress($address);
    }
    #endregion

    #region GETTERS
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function getDateOfBirth(): Carbon
    {
        return $this->dateOfBirth;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
    #endregion

    #region SETTERS
    public function setFirstName(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('First name cannot be empty.');
        }
        if (mb_strlen($value) > config('data.max_name_length')) {
            throw new InvalidArgumentException('First name cannot exceed ' . config('data.max_name_length') . ' characters.');
        }
        $this->firstName = $value;
    }

    public function setMiddleName(string $value): void
    {
        if (mb_strlen($value) > config('data.max_name_length')) {
            throw new InvalidArgumentException('Middle name cannot exceed ' . config('data.max_name_length') . ' characters.');
        }
        $this->middleName = $value;
    }

    public function setLastName(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Last name cannot be empty.');
        }
        if (mb_strlen($value) > config('data.max_name_length')) {
            throw new InvalidArgumentException('Last name cannot exceed ' . config('data.max_name_length') . ' characters.');
        }
        $this->lastName = $value;
    }

    public function setGender(Gender $value): void
    {
        $this->gender = $value;
    }

    public function setDateOfBirth(Carbon $value): void
    {
        $this->dateOfBirth = $value;
    }

    public function setAddress(Address $value): void
    {
        $this->address = $value;
    }
    #endregion

    #region UTILITIES
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'firstName' => $this->getFirstName(),
            'middleName' => $this->getMiddleName(),
            'lastName' => $this->getLastName(),
            'gender' => $this->getGender()->value,
            'dateOfBirth' => $this->getDateOfBirth()->toDateTimeString(),
            'address' => $this->getAddress()->toArray(),
        ]);
    }

    public static function fromArray(array $data): self
    {
        check_array_keys(
            array_keys(get_class_vars(self::class)),
            $data,
            class_basename(self::class)
        );

        return new self(
            $data['username'],
            $data['email'],
            $data['phoneNumber'],
            Status::from($data['status']),
            $data['firstName'],
            $data['middleName'],
            $data['lastName'],
            Gender::from($data['gender']),
            Carbon::parse($data['dateOfBirth']),
            Address::fromArray($data['address'])
        );
    }
    #endregion
}