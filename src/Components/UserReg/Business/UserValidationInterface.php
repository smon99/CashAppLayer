<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Global\Persistence\UserDTO;

interface UserValidationInterface
{
    /**
     * @param UserDTO $userDTO
     *
     * @throws UserValidationException If validation criteria is not matched.
     */
    public function validate(UserDTO $userDTO);
}