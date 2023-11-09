<?php declare(strict_types=1);

namespace Test\Global\Business;

use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Components\UserReg\Business\UserValidation;
use App\Global\Business\Container;
use App\Global\Business\DependencyProvider;
use App\Global\Business\Redirect;
use App\Global\Presentation\View;
use PHPUnit\Framework\TestCase;

class DependencyProviderTest extends TestCase
{
    public function testProvide(): void
    {
        $container = new Container();
        $provider = new DependencyProvider();

        $provider->provide($container);

        self::assertSame('/home/simondewendt/PhpstormProjects/CashAppLayer/src/Global/Business/../Presentation/View', $container->get(View::class)->templatePath);

        $this->assertInstanceOf(View::class, $container->get(View::class));
        $this->assertInstanceOf(Redirect::class, $container->get(Redirect::class));

        $this->assertInstanceOf(AccountRepository::class, $container->get(AccountRepository::class));
        $this->assertInstanceOf(UserRepository::class, $container->get(UserRepository::class));

        $this->assertInstanceOf(AccountEntityManager::class, $container->get(AccountEntityManager::class));
        $this->assertInstanceOf(UserEntityManager::class, $container->get(UserEntityManager::class));

        $this->assertInstanceOf(UserValidation::class, $container->get(UserValidation::class));
    }
}