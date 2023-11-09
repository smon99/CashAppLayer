<?php declare(strict_types=1);

namespace Test\Global\Communication;

use App\Global\Business\Container;
use App\Global\Communication\ErrorController;
use App\Global\Presentation\View;
use PHPUnit\Framework\TestCase;

class ErrorControllerTest extends TestCase
{
    public function testAction(): void
    {
        $container = $this->createMock(Container::class);
        $view = $this->createMock(View::class);

        $container->expects($this->once())
            ->method('get')
            ->with(View::class)
            ->willReturn($view);

        $view->expects($this->once())
            ->method('addParameter')
            ->with('parameters', []);

        $view->expects($this->once())
            ->method('setTemplate')
            ->with('unknown.twig');

        $errorController = new ErrorController($container);
        $result = $errorController->action();
        $this->assertSame($view, $result);
    }
}
