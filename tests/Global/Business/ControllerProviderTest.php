<?php declare(strict_types=1);

namespace Test\Global\Business;

use App\Components\Account\Communication\AccountController;
use App\Components\Account\Communication\HistoryController;
use App\Components\Account\Communication\TransactionController;
use App\Components\UserLog\Communication\LoginController;
use App\Components\UserReg\Communication\UserController;
use App\Global\Business\ControllerProvider;
use App\Global\Communication\ErrorController;
use App\Global\Communication\FeatureController;
use PHPUnit\Framework\TestCase;

class ControllerProviderTest extends TestCase
{
    public function testGetList(): void
    {
        $provider = new ControllerProvider();
        $controllerList = $provider->getList();

        $this->assertIsArray($controllerList);
        $this->assertCount(7, $controllerList);

        $this->assertArrayHasKey('account', $controllerList);
        $this->assertArrayHasKey('login', $controllerList);
        $this->assertArrayHasKey('user', $controllerList);
        $this->assertArrayHasKey('unknown', $controllerList);
        $this->assertArrayHasKey('transaction', $controllerList);

        $this->assertSame(AccountController::class, $controllerList['account']);
        $this->assertSame(LoginController::class, $controllerList['login']);
        $this->assertSame(UserController::class, $controllerList['user']);
        $this->assertSame(ErrorController::class, $controllerList['unknown']);
        $this->assertSame(TransactionController::class, $controllerList['transaction']);
        $this->assertSame(FeatureController::class, $controllerList['feature']);
        $this->assertSame(HistoryController::class, $controllerList['history']);
    }
}