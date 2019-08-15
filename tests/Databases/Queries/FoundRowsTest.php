<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Queries;

use AbterPhp\Framework\TestCase\Database\QueryTestCase;

class FoundRowsTest extends QueryTestCase
{
    /** @var FoundRows - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new FoundRows($this->connectionPoolMock);
    }

    public function testGet()
    {
        $expectedResult = 10;

        $mockStatement = $this->createReadColumnStatement(
            [],
            "$expectedResult",
            QueryTestCase::EXPECTATION_ANY,
            QueryTestCase::EXPECTATION_ANY,
            QueryTestCase::EXPECTATION_ANY
        );

        $this->readConnectionMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($mockStatement);

        $actualResult = $this->sut->get();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetReturnsZeroIfExecuteFails()
    {
        $returnValue = 10;
        $expectedResult = 0;

        $mockStatement = $this->createReadColumnStatement(
            [],
            "$returnValue",
            QueryTestCase::EXPECTATION_ANY,
            QueryTestCase::EXPECTATION_ANY,
            QueryTestCase::EXPECTATION_ANY,
            false
        );

        $this->readConnectionMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($mockStatement);

        $actualResult = $this->sut->get();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
