<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Queries;

use AbterPhp\Framework\Orm\DataMappers\SqlTestCase;

class FoundRowsTest extends SqlTestCase
{
    /** @var FoundRows System Under Test */
    protected $sut;

    public function setUp()
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
            SqlTestCase::EXPECTATION_ANY,
            SqlTestCase::EXPECTATION_ANY,
            SqlTestCase::EXPECTATION_ANY
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
            SqlTestCase::EXPECTATION_ANY,
            SqlTestCase::EXPECTATION_ANY,
            SqlTestCase::EXPECTATION_ANY,
            false
        );

        $this->readConnectionMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($mockStatement);

        $actualResult = $this->sut->get();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param array  $expectedData
     * @param object $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
    }
}
