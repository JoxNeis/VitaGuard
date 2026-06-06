<?php

namespace App\Data\Account;

use App\Data\Value\Account\Role;
use App\Data\Value\Account\Status;

class FacilityAdmin extends User
{
    #region FIELDS
    //TODO: CREATE FACILITY
    private $facility;
    #endregion

    #region CONSTRUCTOR
    public function __construct(
        string $username,
        string $email,
        string $phoneNumber,
        Status $status = Status::ACTIVE
    ) {
        parent::__construct($username, $email, $phoneNumber, Role::FACILITY_ADMIN, $status);
    }
    #endregion

    #region UTILITIES
    public function toArray(): array
    {
        return parent::toArray();
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
            Status::from($data['status'])
        );
    }
    #endregion
}