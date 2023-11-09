<?php declare(strict_types=1);

namespace App\Components\Account\Business;

use App\Global\Persistence\AccountDTO;

class PrepareDeposit
{
    public function prepareDeposit(float $value, int $userID): AccountDTO
    {
        $accountDTO = new AccountDTO();
        $accountDTO->userID = $userID;
        $accountDTO->purpose = "deposit";
        $accountDTO->transactionTime = date('H:i:s');
        $accountDTO->transactionDate = date('Y-m-d');
        $accountDTO->value = $value;

        return $accountDTO;
    }
}