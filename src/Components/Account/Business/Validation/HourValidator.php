<?php declare(strict_types=1);

namespace App\Components\Account\Business\Validation;

use App\Components\Account\Persistence\AccountRepository;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;

class HourValidator implements AccountValidationInterface
{
    public function validate(float $amount, int $userID): void
    {
        $repository = new AccountRepository(new SqlConnector(), new AccountMapper());
        $hourBalance = $repository->calculateBalancePerHour($userID);

        $limit = $hourBalance + $amount;

        if ($limit > 100.0) {
            throw new AccountValidationException('Stündliches Einzahlungslimit von 100€ überschritten!');
        }
    }
}
