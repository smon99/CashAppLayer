<?php declare(strict_types=1);

namespace App\Components\Account\Business;

use App\Global\Persistence\AccountDTO;
use App\Global\Persistence\UserDTO;

class PrepareTransaction
{
    public function prepareTransaction(float $value, UserDTO $userDTO, UserDTO $receiverDTO): array
    {
        $sender = new AccountDTO();
        $receiver = new AccountDTO();

        $sender->userID = $userDTO->userID;
        $sender->purpose = 'Geldtransfer an ' . $receiverDTO->username;
        $sender->transactionTime = date('H:i:s');
        $sender->transactionDate = date('Y-m-d');
        $sender->value = $value * (-1);

        $receiver->userID = $receiverDTO->userID;
        $receiver->purpose = 'Zahlung erhalten von ' . $userDTO->username;
        $receiver->transactionTime = date('H:i:s');
        $receiver->transactionDate = date('Y-m-d');
        $receiver->value = $value;

        return [
            "sender" => $sender,
            "receiver" => $receiver,
        ];
    }
}