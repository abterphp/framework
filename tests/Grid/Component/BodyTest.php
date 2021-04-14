<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Row\Row;
use AbterPhp\Framework\TestDouble\Domain\Entity\FooBarStub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BodyTest extends TestCase
{
    public function testSetEntitiesWithEmptyEntities(): void
    {
        $sut = new Body([], null);

        $sut->setEntities([]);

        $this->assertCount(0, $sut);
    }

    public function testSetEntitiesWithoutGetters(): void
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $sut = new Body([], null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(0, $sut[0]->getCells());
        $this->assertCount(0, $sut[1]->getCells());
    }

    public function testSetEntitiesWithPropertyGetters(): void
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => 'getBar',
        ];

        $sut = new Body($getters, null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(2, $sut[0]->getCells());
        $this->assertCount(2, $sut[1]->getCells());
    }

    public function testSetEntitiesWithCallableGetters(): void
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => fn ($entity) => $entity->getFoo(),
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, null);

        $sut->setEntities($entities);

        $this->assertCount(2, $sut);
        $this->assertInstanceOf(Row::class, $sut[0]);
        $this->assertInstanceOf(Row::class, $sut[1]);
        $this->assertCount(2, $sut[0]->getCells());
        $this->assertCount(2, $sut[1]->getCells());
    }

    public function testSetEntitiesWithMixedGetters(): void
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, null);

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
     * @return FooBarStub|MockObject
     */
    private function createEntity(string $string, int $entityId, string $foo = '', string $bar = '')
    {
        /** @var FooBarStub|MockObject $entity */
        $entity = $this->createMock(FooBarStub::class);

        $entity->expects($this->any())->method('__toString')->willReturn($string);
        $entity->expects($this->any())->method('getId')->willReturn($entityId);
        $entity->expects($this->any())->method('setId');
        $entity->expects($this->any())->method('getFoo')->willReturn($foo);
        $entity->expects($this->any())->method('getBar')->willReturn($bar);

        return $entity;
    }

    public function testGetExtendedNodesWithoutActions(): void
    {
        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, null);

        $nodes = $sut->getExtendedNodes();

        $this->assertSame([], $nodes);
    }

    public function testGetExtendedNodesWithActions(): void
    {
        $actions = new Actions();

        $entities   = [];
        $entities[] = $this->createEntity('foo', 1);
        $entities[] = $this->createEntity('bar', 2);

        $getters = [
            'foo' => 'getFoo',
            'bar' => [$entities[0], 'getBar'],
        ];

        $sut = new Body($getters, $actions);

        $nodes = $sut->getExtendedNodes();

        $this->assertSame([$actions], $nodes);
    }
}
