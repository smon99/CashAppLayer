<?php declare(strict_types=1);

namespace Test\Components\UserReg\Communication;

use App\Components\User\Persistence\UserRepository;
use App\Components\UserReg\Communication\UserController;
use App\Global\Business\Container;
use App\Global\Business\DependencyProvider;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserMapper;
use App\Global\Presentation\View;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private SqlConnector $sqlConnector;

    protected function setUp(): void
    {
        $container = new Container();
        $provider = new DependencyProvider();
        $provider->provide($container);

        $this->sqlConnector = new SqlConnector();
        $userMapper = new UserMapper();

        $this->userRepository = new UserRepository($this->sqlConnector, $userMapper);

        $this->container = $container;
        $this->controller = new UserController($this->container);
    }

    protected function tearDown(): void
    {
        $this->sqlConnector->execute("DELETE FROM Users;", []);
        unset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['register']);
    }

    public function testActionInstance(): void
    {
        self::assertInstanceOf(View::class, $this->controller->action());
    }

    public function testActionRegistration(): void
    {
        $_POST['username'] = 'Tester';
        $_POST['email'] = 'Tester@Tester.de';
        $_POST['password'] = 'Tester123#';

        $_POST['register'] = true;

        $parameters = $key['parameters'] = $this->controller->action()->getParameters();

        $registeredUser = $this->userRepository->findByMail('Tester@Tester.de');

        self::assertContains('Tester', $parameters);
        self::assertContains('Tester@Tester.de', $parameters);
        self::assertContains('Tester123#', $parameters);
        self::assertSame('Tester', $registeredUser->username);
        //url assertion missing
    }

    public function testActionValidationException(): void
    {
        $_POST['username'] = 'Tester';
        $_POST['email'] = 'TesterTester.de';
        $_POST['password'] = 'Tester123#';

        $_POST['register'] = true;

        self::assertContains('Bitte gültige eMail eingeben! ', $this->controller->action()->getParameters());
    }

    public function testActionTemplatePath(): void
    {
        self::assertSame('user.twig', $this->controller->action()->getTpl());
    }
}