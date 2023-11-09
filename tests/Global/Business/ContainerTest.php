<?php declare(strict_types=1);

namespace Test\Global\Business;

use App\Global\Business\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $container = new Container();
        $object = new \stdClass();

        $container->set(\stdClass::class, $object);
        $retrievedObject = $container->get(\stdClass::class);

        $this->assertSame($object, $retrievedObject);
    }

    public function testGetList(): void
    {
        $container = new Container();
        $object1 = new \stdClass();
        $object2 = new \stdClass();

        $container->set('object1', $object1);
        $container->set('object2', $object2);

        $objectList = $container->getList();

        $this->assertCount(2, $objectList);
        $this->assertArrayHasKey('object1', $objectList);
        $this->assertArrayHasKey('object2', $objectList);
    }
}