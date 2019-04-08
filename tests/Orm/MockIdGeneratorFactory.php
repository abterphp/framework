<?php

namespace AbterPhp\Framework\Orm;

use Opulence\Orm\Ids\Generators\IIdGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockIdGeneratorFactory
{
    /**
     * @param TestCase $testCase
     * @param string   ...$ids
     *
     * @return IIdGenerator|MockObject
     */
    public static function create(TestCase $testCase, string ...$ids): IIdGenerator
    {
        /** @var IIdGenerator $idGeneratorMock */
        $idGeneratorMock = $testCase->getMockBuilder(IIdGenerator::class)
            ->setMethods(['generate', 'getEmptyValue', 'isPostInsert'])
            ->getMock();

        foreach ($ids as $idx => $returnValue) {
            $idGeneratorMock->expects($testCase->at($idx))->method('generate')->willReturn($returnValue);
        }

        return $idGeneratorMock;
    }
}
