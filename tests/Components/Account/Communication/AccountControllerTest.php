<?php declare(strict_types=1);

namespace Test\Components\Account\Communication;

use App\Components\Account\Communication\AccountController;
use App\Components\Account\Persistence\AccountRepository;
use App\Global\Business\Container;
use App\Global\Business\DependencyProvider;
use App\Global\Business\Redirect;
use App\Global\Business\Session;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use PHPUnit\Framework\TestCase;

class AccountControllerTest extends TestCase
{
    private Session $session;
    private UserDTO $userDTO;
    private AccountRepository $accountRepository;

    protected function setUp(): void
    {
        $container = new Container();
        $provider = new DependencyProvider();
        $provider->provide($container);

        $this->session = new Session();
        $this->accountRepository = new AccountRepository(new SqlConnector(), new AccountMapper());

        $this->container = $container;
        $this->controller = new AccountController($this->container);

        $this->userDTO = new UserDTO();
        $this->userDTO->password = '$2y$10$rqTcf57sIEVAZsertDU7P.8O3kObwxc17jL6Cec.6oMcX/VWdFX0i';
        $this->userDTO->username = 'Simon';
        $this->userDTO->email = 'Simon@Simon.de';
        $this->userDTO->userID = 4;

        session_start();
    }

    protected function tearDown(): void
    {
        $connector = new SqlConnector();
        $connector->execute("DELETE FROM Transactions;", []);
        $connector->execute("DELETE FROM Users;", []);
        $this->session->logout();
        unset($_POST["amount"], $_POST["logout"], $this->userDTO, $this->session);
    }

    public function testAction(): void
    {
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $_POST["amount"] = '1';

        $params = $this->controller->action()->getParameters();
        $result[] = $this->accountRepository->fetchAllTransactions();
        $deposit = $result[0][0];

        self::assertSame(1.00, $deposit->value);
        self::assertContains("Simon", $params);
        self::assertContains($this->session->loginStatus(), $params);
        self::assertContains($this->accountRepository->calculateBalance($this->session->getUserID()), $params);
        self::assertContains("Die Transaktion wurde erfolgreich gespeichert!", $params);
    }

    public function testActionException(): void
    {
        unset($_POST["amount"]);
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $_POST["amount"] = '500';

        $viewParams = $this->controller->action()->getParameters();

        self::assertContains("Bitte einen Betrag von mindestens 0.01€ und maximal 50€ eingeben!", $viewParams);
        $this->session->logout();
    }

    public function testActionNoSession(): void
    {
        unset($_POST["amount"]);
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $this->session->logout();
        $this->controller->action();
        //url assertion missing

    }

    public function testActionLogOut(): void
    {
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $_POST["logout"] = true;
        $this->controller->action();
        $loginStatus = $this->session->loginStatus();

        self::assertFalse($loginStatus);
        //url assertion missing
    }

    public function testActionTemplatePath(): void
    {
        self::assertSame('deposit.twig', $this->controller->action()->getTpl());
    }
}