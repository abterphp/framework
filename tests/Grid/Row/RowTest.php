<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Row;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Component\Actions;
use PHPUnit\Framework\MockObject\MockObject;

class RowTest extends \PHPUnit\Framework\TestCase
{
    public function testSetEntitySetsEntityWorksWithoutActions()
    {
        $sut = new Row(new Cells());

        /** @var MockObject|IStringerEntity $mockEntity */
        $mockEntity = $this->getMockBuilder(IStringerEntity::class)
            ->setMethods(['getId', 'setId', '__toString'])
            ->getMock();

        $sut->setEntity($mockEntity);

        $this->assertSame($mockEntity, $sut->getEntity());
    }

    public function testSetEntitySetsEntityOnAllActions()
    {
        /** @var MockObject|IStringerEntity $mockEntity */
        $mockEntity = $this->getMockBuilder(IStringerEntity::class)
            ->setMethods(['getId', 'setId', '__toString'])
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
            ->setMethods(['getId', 'setId', '__toString'])
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

        $actualResult = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertContains('action-0', $actualResult);
        $this->assertContains('action-1', $actualResult);
        $this->assertContains($actualResult, $repeatedResult);
    }
}
