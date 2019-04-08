<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Body;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\Grid\Factory\Table\BodyFactory;
use AbterPhp\Framework\Grid\Factory\Table\HeaderFactory;
use AbterPhp\Framework\Grid\Table\Table;
use PHPUnit\Framework\MockObject\MockObject;

class TableFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateCallsHeaderAndBodyFactories()
    {
        /** @var Header|MockObject $headerMock */
        $headerMock = $this->getMockBuilder(Header::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Body|MockObject $headerMock */
        $bodyMock = $this->getMockBuilder(Body::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var HeaderFactory|MockObject $headerFactoryMock */
        $headerFactoryMock = $this->getMockBuilder(HeaderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $headerFactoryMock->expects($this->once())->method('create')->willReturn($headerMock);

        /** @var BodyFactory|MockObject $bodyFactoryMock */
        $bodyFactoryMock = $this->getMockBuilder(BodyFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $bodyFactoryMock->expects($this->once())->method('create')->willReturn($bodyMock);

        $sut = new TableFactory($headerFactoryMock, $bodyFactoryMock);

        $table = $sut->create([], null, [], '');

        $this->assertInstanceOf(Table::class, $table);
    }
}
