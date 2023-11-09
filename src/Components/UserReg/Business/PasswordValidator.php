<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Global\Persistence\UserDTO;

class PasswordValidator implements UserValidationInterface
{
    public function validate(UserDTO $userDTO): void
    {
        $uppercase = preg_match('@[A-Z]@', $userDTO->password);
        $lowercase = preg_match('@[a-z]@', $userDTO->password);
        $number = preg_match('@[0-9]@', $userDTO->password);
        $specialChar = preg_match('@[^\w]@', $userDTO->password);
        $minLength = 6;

        if (!($uppercase && $lowercase && $number && $specialChar && strlen($userDTO->password) >= $minLength)) {
            throw new UserValidationException('Passwort Anforderungen nicht erfüllt! ');
        }
    }
}
