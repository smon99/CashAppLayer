<?php declare(strict_types=1);

namespace App\Components\Account\Communication;

use App\Components\Account\Business\AccountValidation;
use App\Components\Account\Business\AccountValidationException;
use App\Components\Account\Business\InputTransformer;
use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Global\Business\Container;
use App\Global\Business\Redirect;
use App\Global\Business\Session;
use App\Global\Communication\ControllerInterface;
use App\Global\Persistence\AccountDTO;
use App\Global\Presentation\View;

class AccountController implements ControllerInterface
{
    private View $view;
    private AccountRepository $accountRepository;
    private AccountEntityManager $entityManager;
    private AccountValidation $validator;
    public Redirect $redirect;
    private Session $session;
    private InputTransformer $inputTransformer;
    private $success;

    public function __construct(Container $container)
    {
        $this->view = $container->get(View::class);
        $this->accountRepository = $container->get(AccountRepository::class);
        $this->entityManager = $container->get(AccountEntityManager::class);
        $this->validator = $container->get(AccountValidation::class);
        $this->redirect = $container->get(Redirect::class);
        $this->session = $container->get(Session::class);
        $this->inputTransformer = $container->get(InputTransformer::class);
    }

    public function action(): View
    {
        if (!$this->session->loginStatus()) {
            $this->redirect->redirectTo('http://0.0.0.0:8000/?page=login');
        }

        $activeUser = null;
        $balance = null;

        $input = $_POST["amount"] ?? null;

        if ($input !== null) {
            try {
                $validateThis = $this->inputTransformer->transformInput($input);
                $this->validator->collectErrors($validateThis, $this->session->getUserID());
                $amount = $validateThis;

                $date = date('Y-m-d');
                $time = date('H:i:s');

                $saveData = new AccountDTO();
                $saveData->value = $amount;
                $saveData->userID = $this->session->getUserID();
                $saveData->transactionDate = $date;
                $saveData->transactionTime = $time;
                $saveData->purpose = 'deposit';
                $this->entityManager->saveDeposit($saveData);
                $this->success = "Die Transaktion wurde erfolgreich gespeichert!";
            } catch (AccountValidationException $e) {
                $this->view->addParameter('error', $e->getMessage());
            }
        }

        if (isset($_POST["logout"])) {
            $this->session->logout();
            $this->redirect->redirectTo('http://0.0.0.0:8000/?page=login');
        }

        if ($this->session->loginStatus()) {
            $activeUser = $this->session->getUserName();
            $balance = $this->accountRepository->calculateBalance($this->session->getUserID());
        }

        $this->view->addParameter('balance', $balance);
        $this->view->addParameter('loginStatus', $this->session->loginStatus());
        $this->view->addParameter('activeUser', $activeUser);
        $this->view->addParameter('success', $this->success);

        $this->view->setTemplate('deposit.twig');

        return $this->view;
    }
}
