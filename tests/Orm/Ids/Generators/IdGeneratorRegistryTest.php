<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Orm\Ids\Generators;

use Opulence\Orm\Ids\Generators\IIdGenerator;
use Opulence\Orm\Ids\Generators\UuidV4Generator;
use PHPUnit\Framework\MockObject\MockObject;

class IdGeneratorRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var IdGeneratorRegistry System Under Test */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new IdGeneratorRegistry();
    }

    public function testGetIdGeneratorCreatesGeneratorByDefault()
    {
        $className = 'foo';

        $actualResult = $this->sut->getIdGenerator($className);

        $this->assertInstanceOf(UuidV4Generator::class, $actualResult);
    }

    public function testGetIdGeneratorReturnsPreviouslyCreatedGenerator()
    {
        $className = 'foo';

        $actualResult   = $this->sut->getIdGenerator($className);
        $repeatedResult = $this->sut->getIdGenerator($className);

        $this->assertSame($repeatedResult, $actualResult);
    }

    public function testGetIdGeneratorReturnsRegisteredGenerator()
    {
        /** @var IIdGenerator|MockObject $idGenerator */
        $idGenerator = $this->getMockBuilder(IIdGenerator::class)
            ->setMethods(['generate', 'getEmptyValue', 'isPostInsert'])
            ->getMock();

        $className = 'foo';

        $this->sut->registerIdGenerator($className, $idGenerator);

        $actualResult   = $this->sut->getIdGenerator($className);

        $this->assertSame($idGenerator, $actualResult);
    }
}
