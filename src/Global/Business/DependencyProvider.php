<?php declare(strict_types=1);

namespace App\Global\Business;

use App\Components\Account\Business\AccountValidation;
use App\Components\Account\Business\DayValidator;
use App\Components\Account\Business\HourValidator;
use App\Components\Account\Business\InputTransformer;
use App\Components\Account\Business\SingleValidator;
use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Components\UserReg\Business\EMailValidator;
use App\Components\UserReg\Business\EmptyFieldValidator;
use App\Components\UserReg\Business\PasswordValidator;
use App\Components\UserReg\Business\UserDuplicationValidator;
use App\Components\UserReg\Business\UserValidation;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserMapper;
use App\Global\Presentation\View;

class DependencyProvider
{
    public function provide(Container $container): void
    {
        $container->set(View::class, new View(__DIR__ . '/../Presentation/View'));
        $container->set(Redirect::class, new Redirect(new RedirectRecordings()));
        $container->set(Session::class, new Session());
        $container->set(InputTransformer::class, new InputTransformer());
        $container->set(SqlConnector::class, new SqlConnector());

        //Mapper
        $container->set(UserMapper::class, new UserMapper());
        $container->set(AccountMapper::class, new AccountMapper());

        //Repository
        $container->set(AccountRepository::class, new AccountRepository($container->get(SqlConnector::class), $container->get(AccountMapper::class)));
        $container->set(UserRepository::class, new UserRepository($container->get(SqlConnector::class), $container->get(UserMapper::class)));

        //Entity
        $container->set(AccountEntityManager::class, new AccountEntityManager($container->get(SqlConnector::class), $container->get(AccountMapper::class)));
        $container->set(UserEntityManager::class, new UserEntityManager($container->get(SqlConnector::class), $container->get(UserMapper::class)));

        //Account Validation
        $container->set(SingleValidator::class, new SingleValidator());
        $container->set(DayValidator::class, new DayValidator());
        $container->set(HourValidator::class, new HourValidator());
        $container->set(AccountValidation::class, new AccountValidation($container->get(SingleValidator::class), $container->get(DayValidator::class), $container->get(HourValidator::class)));

        //User Validation
        $container->set(EmptyFieldValidator::class, new EmptyFieldValidator());
        $container->set(EMailValidator::class, new EMailValidator());
        $container->set(PasswordValidator::class, new PasswordValidator());
        $container->set(UserDuplicationValidator::class, new UserDuplicationValidator());
        $container->set(UserValidation::class, new UserValidation($container->get(EmptyFieldValidator::class), $container->get(EMailValidator::class), $container->get(PasswordValidator::class), $container->get(UserDuplicationValidator::class)));
    }
}