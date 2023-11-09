<?php declare(strict_types=1);

namespace App\Components\Account\Persistence;

use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;

class AccountRepository
{
    public function __construct(private SqlConnector $sqlConnector, private AccountMapper $accountMapper)
    {
    }

    public function calculateBalance(int $userID): float
    {
        $accountDTOList = $this->fetchAllTransactions();

        $balance = 0.0;

        foreach ($accountDTOList as $entry) {
            if ($entry->userID === $userID) {
                $balance += $entry->value;
            }
        }
        return $balance;
    }

    public function calculateBalancePerHour(int $userID): float
    {
        $accountDTOList = $this->fetchAllTransactions();

        $balancePerHour = 0.0;

        $date = strtotime(date('Y-m-d'));

        $currentTime = new \DateTime();
        $oneHourAgo = $currentTime->sub(new \DateInterval('PT1H'));

        foreach ($accountDTOList as $entry) {
            if (($entry->userID === $userID) && strtotime($entry->transactionTime) > strtotime($oneHourAgo->format('H:i:s')) && strtotime($entry->transactionDate) === $date) {
                $balancePerHour += $entry->value;
            }
        }
        return $balancePerHour;
    }

    public function calculateBalancePerDay(int $userID): float
    {
        $accountDTOList = $this->fetchAllTransactions();

        $balancePerDay = 0.0;
        $date = date('Y-m-d');

        foreach ($accountDTOList as $entry) {
            if (($entry->userID === $userID) && $entry->transactionDate === $date) {
                $balancePerDay += $entry->value;
            }
        }
        return $balancePerDay;
    }

    public function transactionPerUserID(int $userID): array
    {
        $accountDTOList = $this->fetchAllTransactions();

        $userTransactions = [];

        foreach ($accountDTOList as $entry) {
            if ($entry->userID === $userID) {
                $userTransactions[] = $entry;
            }
        }
        return $userTransactions;
    }

    public function fetchAllTransactions(): array
    {
        $query = "SELECT * FROM Transactions";
        $data = $this->sqlConnector->executeSelectAllQuery($query);
        return $this->accountMapper->sqlToDTO($data);
    }
}
