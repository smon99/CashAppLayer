<?php declare(strict_types=1);

namespace App\Global\Business;

use App\Components\Account\Communication\AccountController;
use App\Components\Account\Communication\HistoryController;
use App\Components\Account\Communication\TransactionController;
use App\Components\UserLog\Communication\LoginController;
use App\Components\UserReg\Communication\UserController;
use App\Global\Communication\ErrorController;
use App\Global\Communication\FeatureController;

class ControllerProvider
{
    public function getList(): array
    {
        return [

            "account" => AccountController::class,

            "login" => LoginController::class,

            "user" => UserController::class,

            "transaction" => TransactionController::class,

            "feature" => FeatureController::class,

            "history" => HistoryController::class,

            "unknown" => ErrorController::class,

        ];
    }
}