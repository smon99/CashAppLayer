<?php declare(strict_types=1);

namespace App\Components\Account\Communication;

use App\Components\Account\Business\AccountFacade;
use App\Components\Account\Business\Validation\AccountValidationException;
use App\Global\Business\Container;
use App\Global\Communication\ControllerInterface;
use App\Global\Presentation\View;

class AccountController implements ControllerInterface
{
    private View $view;
    private AccountFacade $accountFacade;
    private $success;

    public function __construct(Container $container)
    {
        $this->view = $container->get(View::class);
        $this->accountFacade = $container->get(AccountFacade::class);
    }

    public function action(): View
    {
        $activeUser = null;
        $balance = null;

        if (!$this->accountFacade->getSessionLoginStatus()) {
            $this->accountFacade->redirectTo('http://0.0.0.0:8000/?page=login');
        }

        if (isset($_POST["logout"])) {
            $this->accountFacade->performLogout();
            $this->accountFacade->redirectTo('http://0.0.0.0:8000/?page=login');
        }

        $input = $_POST["amount"] ?? null;

        if ($input !== null) {
            $validateThis = $this->accountFacade->transformInput($input);

            try {
                $this->accountFacade->performValidation($validateThis, $this->accountFacade->getSessionUserID());
                $amount = $validateThis;

                $save = $this->accountFacade->prepareDepositAccountDTO($amount, $this->accountFacade->getSessionUserID());
                $this->accountFacade->saveDepositViaEntityManager($save);

                $this->success = "Die Transaktion wurde erfolgreich gespeichert!";
            } catch (AccountValidationException $e) {
                $this->view->addParameter('error', $e->getMessage());
            }
        }

        if ($this->accountFacade->getSessionLoginStatus()) {
            $activeUser = $this->accountFacade->getSessionUserName();
            $balance = $this->accountFacade->calculateBalance();
        }

        $this->view->addParameter('balance', $balance);
        $this->view->addParameter('loginStatus', $this->accountFacade->getSessionLoginStatus());
        $this->view->addParameter('activeUser', $activeUser);
        $this->view->addParameter('success', $this->success);

        $this->view->setTemplate('deposit.twig');

        return $this->view;
    }
}