<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Table;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Component\Body;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\TestDouble\Domain\MockEntityFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TableTest extends TestCase
{
    /** @var Table - System Under Test */
    protected $sut;

    /** @var Body|MockObject */
    protected $body;

    /** @var Header|MockObject */
    protected $header;

    public function setUp():void
    {
        parent::setUp();

        $this->body = $this->createMock(Body::class);

        $this->header = $this->createMock(Header::class);

        $this->sut = new Table($this->body, $this->header);
    }

    public function testToStringContainsHeaders()
    {
        $this->body->expects($this->any())->method('__toString')->willReturn('!A!');
        $this->header->expects($this->any())->method('__toString')->willReturn('!B!');

        $this->assertStringContainsString('B', (string)$this->sut);
    }

    public function testToStringContainsRows()
    {
        $this->body->expects($this->any())->method('__toString')->willReturn('!A!');
        $this->header->expects($this->any())->method('__toString')->willReturn('!B!');

        $this->assertStringContainsString('!B!', (string)$this->sut);
    }

    public function testSetTemplateCanChangeContent()
    {
        $template = '--||--';

        $this->body->expects($this->any())->method('__toString')->willReturn('!A!');
        $this->header->expects($this->any())->method('__toString')->willReturn('!B!');

        $this->sut->setTemplate($template);

        $actualResult = (string)$this->sut;

        $this->assertStringNotContainsString('!A!', $actualResult);
        $this->assertStringNotContainsString('!A!', $actualResult);
        $this->assertStringContainsString($template, $actualResult);
    }

    public function testGetSortedUrlCallsHeader()
    {
        $stubBaseUrl   = '/foo?';
        $stubSortedUrl = '/foo?bar';

        $this->header->expects($this->once())->method('getSortedUrl')->with($stubBaseUrl)->willReturn($stubSortedUrl);

        $actualResult = $this->sut->getSortedUrl($stubBaseUrl);

        $this->assertSame($stubSortedUrl, $actualResult);
    }

    public function testGetSortedConditionsCallsHeader()
    {
        $stubSortedConditions = ['foo', 'bar'];

        $this->header->expects($this->once())->method('getSortConditions')->willReturn($stubSortedConditions);

        $actualResult = $this->sut->getSortConditions();

        $this->assertSame($stubSortedConditions, $actualResult);
    }

    public function testGetSqlParamsCallsHeader()
    {
        $stubQueryParams = ['foo', 'bar'];

        $this->header->expects($this->once())->method('getQueryParams')->willReturn($stubQueryParams);

        $actualResult = $this->sut->getSqlParams();

        $this->assertSame($stubQueryParams, $actualResult);
    }

    public function testSetEntitiesCallsBody()
    {
        $stubEntity = MockEntityFactory::createEntityStub($this);

        $stubEntities = [$stubEntity];

        $this->body->expects($this->once())->method('setEntities')->with($stubEntities);

        $this->sut->setEntities($stubEntities);
    }

    public function testGetExtendedNodes()
    {
        $actualResult = $this->sut->getExtendedNodes();

        $this->assertSame([$this->header, $this->body], $actualResult);
    }
}
