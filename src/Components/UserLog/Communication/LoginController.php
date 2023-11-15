<?php declare(strict_types=1);

namespace App\Components\UserLog\Communication;

use App\Components\UserLog\Buisness\UserLogFacade;
use App\Global\Business\Container;
use App\Global\Communication\ControllerInterface;
use App\Global\Presentation\View;

class LoginController implements ControllerInterface
{
    private View $view;
    private UserLogFacade $userLogFacade;

    public function __construct(Container $container)
    {
        $this->view = $container->get(View::class);
        $this->userLogFacade = $container->get(UserLogFacade::class);
    }

    private function formInput(): array
    {
        $mailCheck = $_POST['mail'];
        $password = $_POST['password'];
        return ['mail' => $mailCheck, 'password' => $password];
    }

    public function action(): View
    {
        $credentials = null;

        if (isset($_POST['login'])) {
            $credentials = $this->formInput();
        }

        if ($credentials !== null) {
            $userDTO = $this->userLogFacade->getFindByMail($credentials['mail']);

            if ($userDTO !== null) {
                $this->userLogFacade->performLogin($userDTO, $credentials['password']);
                $this->userLogFacade->redirectTo('http://0.0.0.0:8000/?page=feature');
            }
        }

        $this->view->addParameter('pageTitle', 'Login Page');
        $this->view->setTemplate('login.twig');
        return $this->view;
    }
}
