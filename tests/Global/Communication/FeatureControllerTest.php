<?php declare(strict_types=1);

namespace Test\Global\Communication;

use App\Global\Business\Container;
use App\Global\Business\DependencyProvider;
use App\Global\Business\Session;
use App\Global\Communication\FeatureController;
use App\Global\Persistence\UserDTO;
use App\Global\Presentation\View;
use PHPUnit\Framework\TestCase;

class FeatureControllerTest extends TestCase
{
    private UserDTO $userDTO;
    private Session $session;

    protected function setUp(): void
    {
        $container = new Container();
        $provider = new DependencyProvider();
        $provider->provide($container);

        $this->session = new Session();

        $this->container = $container;
        $this->controller = new FeatureController($this->container);

        $this->userDTO = new UserDTO();
        $this->userDTO->password = '$2y$10$rqTcf57sIEVAZsertDU7P.8O3kObwxc17jL6Cec.6oMcX/VWdFX0i';
        $this->userDTO->username = 'Simon';
        $this->userDTO->email = 'Simon@Simon.de';
        $this->userDTO->userID = 4;

        session_start();
    }

    protected function tearDown(): void
    {
        unset($_SESSION["username"]);
        session_destroy();
    }

    public function testAction(): void
    {
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $feature = $this->controller->action();

        self::assertInstanceOf(View::class, $feature);
        //url assertion missing
    }

    public function testActionNoSession(): void
    {
        $this->session->logout();
        $this->controller->action();
        //url assertion missing
    }

    public function testActionView(): void
    {
        $this->session->loginUser($this->userDTO, 'Simon123#');

        self::assertContains('Simon', $this->controller->action()->getParameters());
        self::assertSame('feature.twig', $this->controller->action()->getTpl());
    }
}