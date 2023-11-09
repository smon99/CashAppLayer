<?php declare(strict_types=1);

use App\Global\Business\Container;

require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

$container = new Container();

$dependencyProvider = new \App\Global\Business\DependencyProvider();
$dependencyProvider->provide($container);

$controllerProvider = new \App\Global\Business\ControllerProvider();
$page = $_GET['page'] ?? '';

$controller = new \App\Global\Communication\ErrorController($container);

foreach ($controllerProvider->getList() as $key => $controllerClass) {
    if ($key === $page) {
        $controllerCheck = new $controllerClass($container);
        if ($controllerCheck instanceof \App\Global\Communication\ControllerInterface) {
            $controller = $controllerCheck;
            break;
        }
    }
}

$data = $controller->action();
$data->display();
/*
$view = $container->get(View::class);
$view->display();
*/