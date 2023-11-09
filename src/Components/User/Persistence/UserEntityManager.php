<?php declare(strict_types=1);

namespace App\Components\User\Persistence;

use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;

class UserEntityManager
{
    public function __construct(private SqlConnector $sqlConnector, private UserMapper $userMapper)
    {
    }

    public function save(UserDTO $userDTO): void
    {
        $query = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";

        $data = $this->userMapper->dtoToArray($userDTO);

        $params = [
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $data['password'],
        ];

        $this->sqlConnector->execute($query, $params);
    }
}
