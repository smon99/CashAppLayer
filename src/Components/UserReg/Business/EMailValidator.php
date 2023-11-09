<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Global\Persistence\UserDTO;

class EMailValidator implements UserValidationInterface
{
    public function validate(UserDTO $userDTO): void
    {
        if (!filter_var($userDTO->email, FILTER_VALIDATE_EMAIL)) {
            throw new UserValidationException('Bitte gültige eMail eingeben! ');
        }
    }
}
