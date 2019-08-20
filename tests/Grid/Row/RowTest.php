<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Row;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\TestDouble\Domain\MockEntityFactory;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testSetEntitySetsEntityWorksWithoutActions()
    {
        $sut = new Row(new Cells());

        $stubEntity = MockEntityFactory::createEntityStub($this);

        $sut->setEntity($stubEntity);

        $this->assertSame($stubEntity, $sut->getEntity());
    }

    public function testSetEntitySetsEntityOnAllActions()
    {
        $stubEntity = MockEntityFactory::createEntityStub($this);

        $actionCount = 2;

        $actions = new Actions();
        for ($i = 0; $i < $actionCount; $i++) {
            $action = $this->createMock(Action::class);

            $action->expects($this->once())->method('setEntity')->with($stubEntity);

            $actions[] = $action;
        }

        $sut = new Row(new Cells(), $actions);

        $sut->setEntity($stubEntity);
    }

    public function testRender()
    {
        $stubEntity = MockEntityFactory::createEntityStub($this, 'foo', null, 'id-1');

        $actionCount = 2;

        $actions = new Actions();
        for ($i = 0; $i < $actionCount; $i++) {
            $action = $this->createMock(Action::class);

            $action->expects($this->once())->method('setEntity')->with($stubEntity);
            $action->expects($this->atLeastOnce())->method('__toString')->willReturn("action-$i");

            $actions[] = $action;
        }

        $sut = new Row(new Cells(), $actions);

        $sut->setEntity($stubEntity);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertStringContainsString('action-0', $actualResult);
        $this->assertStringContainsString('action-1', $actualResult);
        $this->assertStringContainsString($actualResult, $repeatedResult);
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
