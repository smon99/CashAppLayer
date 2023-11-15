<?php declare(strict_types=1);

namespace App\Components\UserReg\Communication;

use App\Components\UserReg\Business\UserRegFacade;
use App\Components\UserReg\Business\UserValidationException;
use App\Global\Business\Container;
use App\Global\Communication\ControllerInterface;
use App\Global\Persistence\UserDTO;
use App\Global\Presentation\View;

class UserController implements ControllerInterface
{
    private View $view;
    private UserRegFacade $userRegFacade;

    public function __construct(Container $container)
    {
        $this->view = $container->get(View::class);
        $this->userRegFacade = $container->get(UserRegFacade::class);
    }

    public function action(): View
    {
        $errors = [];
        $userCheck = '';
        $mailCheck = '';
        $passwordCheck = '';

        if (isset($_POST['register'])) {
            $userCheck = $_POST['username'];
            $mailCheck = $_POST['email'];
            $passwordCheck = $_POST['password'];

            $validatorDTO = new UserDTO();
            $validatorDTO->username = $userCheck;
            $validatorDTO->email = $mailCheck;
            $validatorDTO->password = $passwordCheck;

            try {
                $this->userRegFacade->performUserValidation($validatorDTO);

                $password = password_hash($passwordCheck, PASSWORD_DEFAULT);

                $userDTO = new UserDTO();
                $userDTO->username = $userCheck;
                $userDTO->email = $mailCheck;
                $userDTO->password = $password;

                $this->userRegFacade->saveUser($userDTO);
                $this->userRegFacade->redirectTo('http://0.0.0.0:8000/?page=login');
            } catch (UserValidationException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->view->addParameter('error', implode(' ', $errors));
        }

        $this->view->addParameter('tempUserName', $userCheck);
        $this->view->addParameter('tempMail', $mailCheck);
        $this->view->addParameter('tempPassword', $passwordCheck);

        $this->view->setTemplate('user.twig');

        return $this->view;
    }
}
