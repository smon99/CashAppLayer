<?php declare(strict_types=1);

namespace App\Components\Account\Communication;

use App\Components\Account\Business\AccountFacade;
use App\Components\Account\Business\Validation\AccountValidationException;
use App\Global\Business\Container;
use App\Global\Communication\ControllerInterface;
use App\Global\Presentation\View;

class TransactionController implements ControllerInterface
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
        $error = null;

        if (!$this->accountFacade->getSessionLoginStatus()) {
            $this->accountFacade->redirectTo('http://0.0.0.0:8000/?page=login');
        }

        if (isset($_POST["logout"])) {
            $this->accountFacade->performLogout();
            $this->accountFacade->redirectTo('http://0.0.0.0:8000/?page=login');
        }

        if (isset($_POST["transfer"])) {
            try {
                $receiver = $this->accountFacade->getFindByMail($_POST["receiver"]);
                $validateThis = $this->accountFacade->transformInput($_POST["amount"]);
                $balance = $this->accountFacade->calculateBalance();

                $this->accountFacade->performValidation($validateThis, $this->accountFacade->getSessionUserID());

                if ($receiver === null) {
                    $error = 'Empfänger existiert nicht! ';
                    $this->view->addParameter('error', $error);
                }

                if ($validateThis > $balance) {
                    $error = 'Guthaben zu gering! ';
                    $this->view->addParameter('error', $error);
                }

                if ($error === null) {
                    $senderDTO = $this->accountFacade->getFindByUsername($this->accountFacade->getSessionUserName());
                    $receiverDTO = $receiver;
                    $transaction = $this->accountFacade->prepareTransaction($validateThis, $senderDTO, $receiverDTO);

                    $this->accountFacade->saveDepositViaEntityManager($transaction["sender"]);
                    $this->accountFacade->saveDepositViaEntityManager($transaction["receiver"]);

                    $this->success = "Die Transaktion wurde erfolgreich durchgeführt!";
                }

            } catch (AccountValidationException $e) {
                $this->view->addParameter('error', $e->getMessage());
            }
        }

        if ($this->accountFacade->getSessionLoginStatus()) {
            $activeUser = $this->accountFacade->getSessionUserName();
            $balance = $this->accountFacade->calculateBalance();
        }

        $this->view->addParameter('activeUser', $activeUser);
        $this->view->addParameter('balance', $balance);
        $this->view->addParameter('loginStatus', $this->accountFacade->getSessionLoginStatus());
        $this->view->addParameter('success', $this->success);

        $this->view->setTemplate('transaction.twig');

        return $this->view;
    }
}