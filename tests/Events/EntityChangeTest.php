<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EntityChangeTest extends TestCase
{
    protected const EVENT_TYPE = 'foo';

    /** @var EntityChange - System Under Test */
    protected EntityChange $sut;

    /** @var IStringerEntity|MockObject */
    protected $entityMock;

    public function setUp(): void
    {
        $this->entityMock = $this->createMock(IStringerEntity::class);

        $this->sut = new EntityChange($this->entityMock, static::EVENT_TYPE);

        parent::setUp();
    }

    public function testGetEntity(): void
    {
        $actualResult = $this->sut->getEntity();

        $this->assertSame($this->entityMock, $actualResult);
    }

    public function testGetEntityId(): void
    {
        $expectedResult = 'foo';

        $this->entityMock->expects($this->once())->method('getId')->willReturn($expectedResult);
        $actualResult = $this->sut->getEntityId();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetEntityName(): void
    {
        $expectedResult = get_class($this->entityMock);

        $actualResult = $this->sut->getEntityName();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetEntityType(): void
    {
        $actualResult = $this->sut->getEventType();

        $this->assertSame(static::EVENT_TYPE, $actualResult);
    }
}
