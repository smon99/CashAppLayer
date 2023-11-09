<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Global\Persistence\UserDTO;

class UserValidation
{
    private array $validationCollection;

    public function __construct(UserValidationInterface ...$validations)
    {
        $this->validationCollection = $validations;
    }

    public function collectErrors(UserDTO $userDTO): void
    {
        $errors = [];

        foreach ($this->validationCollection as $validator) {
            try {
                $validator->validate($userDTO);
            } catch (UserValidationException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new UserValidationException(implode(' ', $errors));
        }
    }
}
