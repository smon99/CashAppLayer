<?php declare(strict_types=1);

namespace App\Components\UserReg\Business;

use App\Components\User\Persistence\UserEntityManager;
use App\Global\Business\Redirect;
use App\Global\Persistence\UserDTO;

class UserRegFacade
{
    public function __construct(
        private UserEntityManager $userEntityManager,
        private UserValidation    $userValidation,
        private Redirect          $redirect
    )
    {
    }

    public function performUserValidation(UserDTO $userDTO): void
    {
        $this->userValidation->collectErrors($userDTO);
    }

    public function saveUser(UserDTO $userDTO): void
    {
        $this->userEntityManager->save($userDTO);
    }

    public function redirectTo(string $url): void
    {
        $this->redirect->redirectTo($url);
    }
}