<?php declare(strict_types=1);

namespace App\Global\Persistence;

class AccountMapper
{
    public function sqlToDTO($data): array
    {
        $collection = [];

        foreach ($data as $ENTRY) {
            $accountDTO = new AccountDTO();
            $accountDTO->transactionID = $ENTRY["transactionID"];
            $accountDTO->value = $ENTRY["value"];
            $accountDTO->userID = $ENTRY["userID"];
            $accountDTO->transactionDate = $ENTRY["transactionDate"];
            $accountDTO->transactionTime = $ENTRY["transactionTime"];
            $accountDTO->purpose = $ENTRY["purpose"];

            $collection[] = $accountDTO;
        }
        return $collection;
    }

    public function dtoToArray(AccountDTO $accountDTO): array
    {
        return [
            'transactionID' => $accountDTO->transactionID,
            'value' => $accountDTO->value,
            'userID' => $accountDTO->userID,
            'transactionDate' => $accountDTO->transactionDate,
            'transactionTime' => $accountDTO->transactionTime,
            'purpose' => $accountDTO->purpose,
        ];
    }
}
