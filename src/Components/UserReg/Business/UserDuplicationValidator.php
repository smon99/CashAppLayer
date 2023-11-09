<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Components\User\Persistence\UserRepository;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;

class UserDuplicationValidator implements UserValidationInterface
{
    public function validate(UserDTO $userDTO): void
    {
        $repository = new UserRepository(new SqlConnector(), new UserMapper());

        if ($repository->findByMail($userDTO->email) !== null) {
            throw new UserValidationException('Fehler eMail bereits vergeben! ');
        }
        if ($repository->findByUsername($userDTO->username) !== null) {
            throw new UserValidationException('Fehler Name bereits vergeben! ');
        }
    }
}
