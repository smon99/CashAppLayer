<?php declare(strict_types=1);

namespace App\Components\Account\Business\Validation;

use App\Components\Account\Persistence\AccountRepository;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;

class DayValidator implements AccountValidationInterface
{
    public function validate(float $amount, int $userID): void
    {
        $repository = new AccountRepository(new SqlConnector(), new AccountMapper());
        $dayBalance = $repository->calculateBalancePerDay($userID);

        $limit = $dayBalance + $amount;

        if ($limit > 500.0) {
            throw new AccountValidationException('Tägliches Einzahlungslimit von 500€ überschritten!');
        }
    }
}
