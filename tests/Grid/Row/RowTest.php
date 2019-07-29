<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Row;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Node;
use PHPUnit\Framework\MockObject\MockObject;

class RowTest extends \PHPUnit\Framework\TestCase
{
    public function testSetEntitySetsEntityWorksWithoutActions()
    {
        $sut = new Row(new Cells());

        /** @var MockObject|IStringerEntity $mockEntity */
        $mockEntity = $this->getMockBuilder(IStringerEntity::class)
            ->setMethods(['getId', 'setId', '__toString', 'toJSON'])
            ->getMock();

        $sut->setEntity($mockEntity);

        $this->assertSame($mockEntity, $sut->getEntity());
    }

    public function testSetEntitySetsEntityOnAllActions()
    {
        /** @var MockObject|IStringerEntity $mockEntity */
        $mockEntity = $this->getMockBuilder(IStringerEntity::class)
            ->setMethods(['getId', 'setId', '__toString', 'toJSON'])
            ->getMock();

        $actionCount = 2;

        $actions = new Actions();
        for ($i = 0; $i < $actionCount; $i++) {
            $action = $this->getMockBuilder(Action::class)
                ->disableOriginalConstructor()
                ->setMethods(['setEntity'])
                ->getMock();

            $action->expects($this->once())->method('setEntity')->with($mockEntity);

            $actions[] = $action;
        }

        $sut = new Row(new Cells(), $actions);

        $sut->setEntity($mockEntity);
    }

    public function testRender()
    {
        /** @var MockObject|IStringerEntity $mockEntity */
        $mockEntity = $this->getMockBuilder(IStringerEntity::class)
            ->setMethods(['getId', 'setId', '__toString', 'toJSON'])
            ->getMock();
        $mockEntity->expects($this->any())->method('__toString')->willReturn('foo');
        $mockEntity->expects($this->any())->method('getId')->with(1);

        $actionCount = 2;

        $actions = new Actions();
        for ($i = 0; $i < $actionCount; $i++) {
            $action = $this->getMockBuilder(Action::class)
                ->disableOriginalConstructor()
                ->setMethods(['setEntity', '__toString'])
                ->getMock();

            $action->expects($this->once())->method('setEntity')->with($mockEntity);
            $action->expects($this->atLeastOnce())->method('__toString')->willReturn("action-$i");

            $actions[] = $action;
        }

        $sut = new Row(new Cells(), $actions);

        $sut->setEntity($mockEntity);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertContains('action-0', $actualResult);
        $this->assertContains('action-1', $actualResult);
        $this->assertContains($actualResult, $repeatedResult);
    }

    public function testGetNodes()
    {
        $cells   = new Cells(new Cell(new Component('abc', [], [], Html5::TAG_I), 'foo-group'));
        $actions = new Actions();

        $sut = new Row($cells, $actions);

        $nodes = $sut->getNodes();

        $this->assertCount(0, $nodes);
    }

    public function testGetExtendedNodes()
    {
        $cells   = new Cells(new Cell(new Component('abc', [], [], Html5::TAG_I), 'foo-group'));
        $actions = new Actions();

        $sut = new Row($cells, $actions);

        $nodes = $sut->getExtendedNodes();

        $this->assertCount(2, $nodes);
        $this->assertSame($cells, $nodes[0]);
        $this->assertInstanceOf(Cell::class, $nodes[1]);
    }

    public function testGetDescendantNodes()
    {
        $cells   = new Cells(new Cell(new Component('abc', [], [], Html5::TAG_I), 'foo-group'));
        $actions = new Actions();

        $sut = new Row($cells, $actions);

        $nodes = $sut->getDescendantNodes();

        $this->assertCount(0, $nodes);
    }

    public function testGetExtendedDescendantNodes()
    {
        $node      = new Node('abc');
        $component = new Component($node, [], [], Html5::TAG_I);
        $cell      = new Cell($component, 'foo-group');
        $cells     = new Cells($cell);
        $actions   = new Actions();

        $sut = new Row($cells, $actions);

        $nodes = $sut->getExtendedDescendantNodes();

        $this->assertCount(6, $nodes);
        $this->assertSame($cells, $nodes[0]);
        $this->assertSame($cell, $nodes[1]);
        $this->assertSame($component, $nodes[2]);
        $this->assertSame($node, $nodes[3]);
        $this->assertEquals(new Cell($actions, Cell::GROUP_ACTIONS, [Cell::INTENT_ACTIONS]), $nodes[4]);
        $this->assertEquals($actions, $nodes[5]);
    }
}
