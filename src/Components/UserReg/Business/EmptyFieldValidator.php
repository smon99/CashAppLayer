<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Global\Persistence\UserDTO;

class EmptyFieldValidator implements UserValidationInterface
{
    public function validate(UserDTO $userDTO): void
    {
        if (empty($userDTO->username) || empty($userDTO->email) || empty($userDTO->password)) {
            throw new UserValidationException('Alle Felder müssen ausgefüllt sein! ');
        }
    }
}
