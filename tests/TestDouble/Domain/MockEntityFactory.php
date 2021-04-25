<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble\Domain;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockEntityFactory
{
    /**
     * @param TestCase    $testCase
     * @param string|null $toString
     * @param string|null $toJson
     * @param string|null $entityId
     * @param string|null $className
     *
     * @return IStringerEntity|MockObject
     */
    public static function createEntityStub(
        TestCase $testCase,
        ?string $toString = null,
        ?string $toJson = null,
        ?string $entityId = null,
        string $className = IStringerEntity::class
    ) {
        /** @var IStringerEntity|MockObject $entity */
        $entity = $testCase->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        if (null !== $toString) {
            $entity->expects($testCase->any())->method('__toString')->willReturn($toString);
        }
        if (null !== $toJson) {
            $entity->expects($testCase->any())->method('toJSON')->willReturn($toJson);
        }
        if (null !== $toString) {
            $entity->expects($testCase->any())->method('getId')->willReturn($entityId);
        }

        return $entity;
    }
}
