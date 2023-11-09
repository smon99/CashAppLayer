<?php declare(strict_types=1);

namespace App\Global\Persistence;

class UserDTO
{
    public int $userID = 1;
    public string $username = '';
    public string $email = '';
    public string $password = '';
}