<?php declare(strict_types=1);

namespace Test\Global\Presentation;

use App\Global\Presentation\View;
use FilesystemIterator;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    protected function tearDown(): void
    {
        $templatePath = __DIR__ . '/temp_templates';

        if (is_dir($templatePath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($templatePath, FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($templatePath);
        }
        parent::tearDown();
    }

    public function testDisplay(): void
    {
        $templatePath = __DIR__ . '/temp_templates';
        mkdir($templatePath);
        file_put_contents($templatePath . '/test_template.twig', 'Hello, {{ name }}!');

        $view = new View($templatePath);

        $view->setTemplate('test_template.twig');
        $view->addParameter('name', 'John');

        ob_start();
        $view->display();
        $output = ob_get_clean();

        $this->assertSame('Hello, John!', $output);
    }

    public function testGetParameters(): void
    {
        $templatePath = __DIR__ . '/temp_templates';
        mkdir($templatePath);

        $view = new View($templatePath);
        $view->addParameter('param1', 'value1');
        $view->addParameter('param2', 'value2');

        $parameters = $view->getParameters();

        $this->assertIsArray($parameters);
        $this->assertCount(2, $parameters);
        $this->assertArrayHasKey('param1', $parameters);
        $this->assertArrayHasKey('param2', $parameters);
        $this->assertSame('value1', $parameters['param1']);
        $this->assertSame('value2', $parameters['param2']);

        rmdir($templatePath);
    }

    public function testGetTpl(): void
    {
        $templatePath = __DIR__ . '/temp_templates';
        mkdir($templatePath);

        $view = new View($templatePath);
        $view->setTemplate('test_template.twig');

        $this->assertSame('test_template.twig', $view->getTpl());

        rmdir($templatePath);
    }
}