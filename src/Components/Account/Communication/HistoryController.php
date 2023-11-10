<?php declare(strict_types=1);

namespace App\Components\Account\Communication;

use App\Components\Account\Business\AccountFacade;
use App\Global\Business\Container;
use App\Global\Communication\ControllerInterface;
use App\Global\Presentation\View;

class HistoryController implements ControllerInterface
{
    private View $view;
    private AccountFacade $accountFacade;

    public function __construct(Container $container)
    {
        $this->view = $container->get(View::class);
        $this->accountFacade = $container->get(AccountFacade::class);
    }

    public function action(): View
    {
        if (!$this->accountFacade->getSessionLoginStatus()) {
            $this->accountFacade->redirectTo('http://0.0.0.0:8000/?page=login');
        } else {
            $transactions = $this->accountFacade->getTransactionsPerUserID($this->accountFacade->getSessionUserID());
            $this->view->addParameter('transactions', $transactions);
        }

        $this->view->setTemplate('history.twig');

        return $this->view;
    }
}