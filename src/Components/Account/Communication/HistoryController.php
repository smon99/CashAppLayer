<?php declare(strict_types=1);

namespace App\Components\Account\Communication;

use App\Components\Account\Persistence\AccountRepository;
use App\Global\Business\Container;
use App\Global\Business\Redirect;
use App\Global\Business\Session;
use App\Global\Communication\ControllerInterface;
use App\Global\Presentation\View;

class HistoryController implements ControllerInterface
{
    private View $view;
    public Redirect $redirect;
    private AccountRepository $accountRepository;
    private Session $session;

    public function __construct(Container $container)
    {
        $this->view = $container->get(View::class);
        $this->redirect = $container->get(Redirect::class);
        $this->accountRepository = $container->get(AccountRepository::class);
        $this->session = $container->get(Session::class);
    }

    public function action(): View
    {
        if (!$this->session->loginStatus()) {
            $this->redirect->redirectTo('http://0.0.0.0:8000/?page=login');
        } else {
            $transactions = $this->accountRepository->transactionPerUserID($this->session->getUserID());
            $this->view->addParameter('transactions', $transactions);
        }

        $this->view->setTemplate('history.twig');

        return $this->view;
    }
}