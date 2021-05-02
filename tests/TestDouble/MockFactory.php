<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockFactory
{
    /**
     * @param TestCase $testCase
     * @param string   $className
     * @param array    $expectsAny
     * @param array    $expectsExactly
     * @param array    $expectsAt
     *
     * @return MockObject
     */
    public static function createMock(
        TestCase $testCase,
        string $className,
        $expectsAny = [],
        $expectsExactly = [],
        $expectsAt = []
    ): MockObject {
        $entity = $testCase->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($expectsAny as $method => $willReturn) {
            $entity->expects($testCase->any())->method($method)->willReturn($willReturn);
        }

        foreach ($expectsExactly as $method => $methodExpectations) {
            foreach ($methodExpectations as $exactly => $willReturn) {
                $entity->expects($testCase->exactly($exactly))->method($method)->willReturn($willReturn);
            }
        }

        foreach ($expectsAt as $method => $methodExpectations) {
            $entity
                ->expects($testCase->exactly(count($methodExpectations)))
                ->method($method)
                ->willReturnOnConsecutiveCalls($methodExpectations);
        }

        return $entity;
    }
}
