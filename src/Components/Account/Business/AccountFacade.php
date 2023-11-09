<?php declare(strict_types=1);

namespace App\Components\Account\Business;

use App\Components\Account\Business\Validation\AccountValidation;
use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Global\Business\Container;
use App\Global\Business\Redirect;
use App\Global\Business\Session;
use App\Global\Persistence\AccountDTO;
use Exception;

class AccountFacade
{
    private Session $session;
    private AccountRepository $accountRepository;
    private AccountEntityManager $accountEntityManager;
    private AccountValidation $accountValidation;
    private InputTransformer $inputTransformer;
    private Redirect $redirect;

    public function __construct(Container $container)
    {
        $this->session = $container->get(Session::class);
        $this->accountRepository = $container->get(AccountRepository::class);
        $this->accountEntityManager = $container->get(AccountEntityManager::class);
        $this->accountValidation = $container->get(AccountValidation::class);
        $this->inputTransformer = $container->get(InputTransformer::class);
        $this->redirect = $container->get(Redirect::class);
    }

    public function getSessionLoginStatus(): bool
    {
        return $this->session->loginStatus();
    }

    public function getSessionUserName(): string
    {
        return $this->session->getUserName();
    }

    public function getSessionUserID(): int
    {
        return $this->session->getUserID();
    }

    public function calculateBalance(): float
    {
        return $this->accountRepository->calculateBalance($this->getSessionUserID());
    }

    public function saveDepositViaEntityManager(AccountDTO $accountDTO): void
    {
        $this->accountEntityManager->saveDeposit($accountDTO);
    }

    public function performValidation(float $value, int $userID): ?string
    {
        try {
            $this->accountValidation->collectErrors($value, $userID);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function transformInput(string $input): float
    {
        return $this->inputTransformer->transformInput($input);
    }

    public function redirectTo(string $url): void
    {
        $this->redirect->redirectTo($url);
    }
}