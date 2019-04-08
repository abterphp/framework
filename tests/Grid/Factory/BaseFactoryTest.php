<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Grid;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\I18n\MockTranslatorFactory;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;

class BaseFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var BaseFactory|MockObject */
    protected $sut;

    /** @var UrlGenerator|MockObject */
    protected $urlGeneratorMock;

    /** @var PaginationFactory|MockObject */
    protected $paginationFactoryMock;

    /** @var TableFactory|MockObject */
    protected $tableFactoryMock;

    /** @var GridFactory|MockObject */
    protected $gridFactoryMock;

    /** @var Filters|MockObject */
    protected $filtersMock;

    public function setUp()
    {
        $this->urlGeneratorMock = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFromName'])
            ->getMock();

        $this->paginationFactoryMock = $this->getMockBuilder(PaginationFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->tableFactoryMock = $this->getMockBuilder(TableFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->gridFactoryMock = $this->getMockBuilder(GridFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->filtersMock = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->setMethods(['setParams', 'getUrl'])
            ->getMock();


        $this->sut = $this->getMockForAbstractClass(
            BaseFactory::class,
            [
                $this->urlGeneratorMock,
                $this->paginationFactoryMock,
                $this->tableFactoryMock,
                $this->gridFactoryMock,
                $this->filtersMock
            ]
        );
    }

    public function testCreateGrid()
    {
        $params  = ['foo' => 'Foo'];
        $baseUrl = '/foo?';

        $this->paginationFactoryMock->expects($this->once())->method('create');
        $this->tableFactoryMock->expects($this->once())->method('create');
        $this->gridFactoryMock->expects($this->once())->method('create');
        $this->filtersMock->expects($this->once())->method('setParams');
        $this->filtersMock->expects($this->once())->method('getUrl');

        $grid = $this->sut->createGrid($params, $baseUrl);

        $this->assertInstanceOf(Grid::class, $grid);
    }
}
