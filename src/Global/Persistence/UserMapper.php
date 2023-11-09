<?php declare(strict_types=1);

namespace App\Global\Persistence;

class UserMapper
{
    public function sqlToDTO($data): array
    {
        $collection = [];

        foreach ($data as $ENTRY) {
            $userDTO = new UserDTO();
            $userDTO->userID = $ENTRY["userID"];
            $userDTO->username = $ENTRY["username"];
            $userDTO->email = $ENTRY["email"];
            $userDTO->password = $ENTRY["password"];

            $collection[] = $userDTO;
        }
        return $collection;
    }

    public function dtoToArray(UserDTO $userDTO): array
    {
        return [
            'userID' => $userDTO->userID,
            'username' => $userDTO->username,
            'email' => $userDTO->email,
            'password' => $userDTO->password,
        ];
    }

}