<?php declare(strict_types=1);

namespace App\Components\Account\Business\Validation;

interface AccountValidationInterface
{
    /**
     * @param float $amount
     *
     * @throws AccountValidationException
     *
     * @return void
     */
    public function validate(float $amount, int $userID): void;
}
