<?php declare(strict_types=1);

namespace Test\Components\Account\Communication;

use App\Components\Account\Communication\HistoryController;
use App\Global\Business\Container;
use App\Global\Business\DependencyProvider;
use App\Global\Business\Session;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Presentation\View;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEmpty;

class HistoryControllerTest extends TestCase
{
    private UserDTO $userDTO;
    private Session $session;
    private HistoryController $controller;
    private SqlConnector $connector;

    protected function setUp(): void
    {
        $container = new Container();
        $provider = new DependencyProvider();
        $provider->provide($container);

        $this->session = new Session();
        $this->connector = new SqlConnector();

        $this->controller = new HistoryController($container);

        $this->userDTO = new UserDTO();
        $this->userDTO->password = '$2y$10$rqTcf57sIEVAZsertDU7P.8O3kObwxc17jL6Cec.6oMcX/VWdFX0i';
        $this->userDTO->username = 'Simon';
        $this->userDTO->email = 'Simon@Simon.de';
        $this->userDTO->userID = 4;

        session_start();
        $this->session->loginUser($this->userDTO, 'Simon123#');
    }

    protected function tearDown(): void
    {
        $this->connector->execute("DELETE FROM Users;", []);
        $this->connector->execute("DELETE FROM Transactions;", []);
        unset($_SESSION["loginStatus"], $_SESSION["userID"]);
        session_destroy();
    }

    public function testAction(): void
    {
        $history = $this->controller->action();

        self::assertInstanceOf(View::class, $history);
        //url assertion missing
    }

    public function testActionNoSession(): void
    {
        $this->session->logout();

        $this->controller->action();
        //url assertion missing
    }

    public function testActionViewParameters(): void
    {
        $viewParams[] = $this->controller->action()->getParameters();
        $assertion = $viewParams[0]['transactions'];

        assertEmpty($assertion);
        self::assertSame('history.twig', $this->controller->action()->getTpl());
    }
}