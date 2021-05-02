<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Row;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\TestDouble\Domain\MockEntityFactory;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testSetEntitySetsEntityWorksWithoutActions(): void
    {
        $sut = new Row(new Cells());

        $stubEntity = MockEntityFactory::createEntityStub($this);

        $sut->setEntity($stubEntity);

        $this->assertSame($stubEntity, $sut->getEntity());
    }

    public function testSetEntitySetsEntityOnAllActions(): void
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

    public function testRender(): void
    {
        $stubEntity = MockEntityFactory::createEntityStub($this, 'foo', null, 'id-1');

        $actionCount = 2;

        $actions = new Actions();
        for ($i = 0; $i < $actionCount; $i++) {
            $actionMock = $this->createMock(Action::class);

            $actionMock->expects($this->once())->method('setEntity')->with($stubEntity);
            $actionMock->expects($this->atLeastOnce())->method('__toString')->willReturn("action-$i");

            $actions[] = $actionMock;
        }

        $sut = new Row(new Cells(), $actions);

        $sut->setEntity($stubEntity);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertStringContainsString('action-0', $actualResult);
        $this->assertStringContainsString('action-1', $actualResult);
        $this->assertStringContainsString($actualResult, $repeatedResult);
    }

    public function testGetNodes(): void
    {
        $expectedResult = 0;

        $cell0 = new Cell(new Tag('abc', [], null, Html5::TAG_I), 'foo-group');
        $cells   = new Cells([$cell0]);
        $actions = new Actions();

        $sut = new Row($cells, $actions);

        $nodes = $sut->getNodes();

        $this->assertCount($expectedResult, $nodes);
    }

    public function testGetExtendedNodes(): void
    {
        $expectedResult = 2;

        $cell0 = new Cell(new Tag('abc', [], null, Html5::TAG_I), 'foo-group');
        $cells   = new Cells([$cell0]);
        $actions = new Actions();

        $sut = new Row($cells, $actions);

        $nodes = $sut->getExtendedNodes();

        $this->assertCount($expectedResult, $nodes);
    }
}
