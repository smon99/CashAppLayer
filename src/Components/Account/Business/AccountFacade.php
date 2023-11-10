<?php declare(strict_types=1);

namespace App\Components\Account\Business;

use App\Components\Account\Business\Validation\AccountValidation;
use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Components\User\Persistence\UserRepository;
use App\Global\Business\Redirect;
use App\Global\Business\Session;
use App\Global\Persistence\AccountDTO;
use App\Global\Persistence\UserDTO;

class AccountFacade
{
    public function __construct(
        private Session              $session,
        private AccountRepository    $accountRepository,
        private UserRepository       $userRepository,
        private AccountEntityManager $accountEntityManager,
        private AccountValidation    $accountValidation,
        private InputTransformer     $inputTransformer,
        private Redirect             $redirect,
        private PrepareDeposit       $prepareDeposit,
        private PrepareTransaction   $prepareTransaction
    )
    {
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

    public function performLogout(): void
    {
        $this->session->logout();
    }

    public function calculateBalance(): float
    {
        return $this->accountRepository->calculateBalance($this->getSessionUserID());
    }

    public function getTransactionsPerUserID(int $userID): array
    {
        return $this->accountRepository->transactionPerUserID($userID);
    }

    public function getFindByMail(string $mail): UserDTO
    {
        return $this->userRepository->findByMail($mail);
    }

    public function getFindByUsername(string $username): UserDTO
    {
        return $this->userRepository->findByUsername($username);
    }

    public function saveDepositViaEntityManager(AccountDTO $accountDTO): void
    {
        $this->accountEntityManager->saveDeposit($accountDTO);
    }

    public function performValidation(float $value, int $userID): void
    {
        $this->accountValidation->collectErrors($value, $userID);
    }

    public function transformInput(string $input): float
    {
        return $this->inputTransformer->transformInput($input);
    }

    public function redirectTo(string $url): void
    {
        $this->redirect->redirectTo($url);
    }

    public function prepareDepositAccountDTO(float $value, int $userID): AccountDTO
    {
        return $this->prepareDeposit->prepareDeposit($value, $userID);
    }

    public function prepareTransaction(float $value, UserDTO $userDTO, UserDTO $receiverDTO): array
    {
        return $this->prepareTransaction->prepareTransaction($value, $userDTO, $receiverDTO);
    }
}