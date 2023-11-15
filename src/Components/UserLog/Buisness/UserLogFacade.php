<?php declare(strict_types=1);

namespace App\Components\UserLog\Buisness;

use App\Components\User\Persistence\UserRepository;
use App\Global\Business\Redirect;
use App\Global\Business\Session;
use App\Global\Persistence\UserDTO;

class UserLogFacade
{
    public function __construct(
        private UserRepository $userRepository,
        private Session        $session,
        private Redirect       $redirect
    )
    {
    }

    public function getFindByMail(string $mail): ?UserDTO
    {
        return $this->userRepository->findByMail($mail);
    }

    public function performLogin(UserDTO $userDTO, string $password): void
    {
        $this->session->loginUser($userDTO, $password);
    }

    public function redirectTo(string $url): void
    {
        $this->redirect->redirectTo($url);
    }
}