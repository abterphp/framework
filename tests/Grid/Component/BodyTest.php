<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Row\Row;
use PHPUnit\Framework\MockObject\MockObject;

class BodyTest extends \PHPUnit\Framework\TestCase
{
    public function testSetEntitiesWithEmptyEntities()
    {
        $sut = new Body([], [], null);

        $sut->setEntities([]);

        $this->assertCount(0, $sut);
    }

    public function testSetEntitiesWithoutGetters()
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $sut = new Body([], [], null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(0, $sut[0]->getCells());
        $this->assertCount(0, $sut[1]->getCells());
    }

    public function testSetEntitiesWithPropertyGetters()
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => 'getBar',
        ];

        $sut = new Body($getters, [], null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(2, $sut[0]->getCells());
        $this->assertCount(2, $sut[1]->getCells());
    }

    public function testSetEntitiesWithCallableGetters()
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => function ($entity) {
                return $entity->getFoo();
            },
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, [], null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(2, $sut[0]->getCells());
        $this->assertCount(2, $sut[1]->getCells());
    }

    public function testSetEntitiesWithMixedGetters()
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, [], null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(2, $sut[0]->getCells());
        $this->assertCount(2, $sut[1]->getCells());
    }

    /**
     * @param string $string
     * @param int    $entityId
     * @param string $foo
     * @param string $bar
     *
     * @return IStringerEntity
     */
    private function createEntity(string $string, int $entityId, string $foo = '', string $bar = ''): IStringerEntity
    {
        /** @var IStringerEntity|MockObject $entity */
        $entity = $this->getMockBuilder(IStringerEntity::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString', 'getId', 'setId', 'getFoo', 'getBar', 'toJSON'])
            ->getMock();

        $entity->expects($this->any())->method('__toString')->willReturn($string);
        $entity->expects($this->any())->method('getId')->willReturn($entityId);
        $entity->expects($this->any())->method('setId');
        $entity->expects($this->any())->method('getFoo')->willReturn($foo);
        $entity->expects($this->any())->method('getBar')->willReturn($bar);

        return $entity;
    }

    public function testGetExtendedNodesWithoutActions()
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, [], null);

        $nodes = $sut->getExtendedNodes();

        $this->assertSame([], $nodes);
    }

    public function testGetExtendedNodesWithActions()
    {
        $actions = new Actions();

        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, [], $actions);

        $nodes = $sut->getExtendedNodes();

        $this->assertSame([$actions], $nodes);
    }
}
