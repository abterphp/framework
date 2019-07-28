<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Console\Commands\Security\SecretGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SecretGeneratorReadyTest extends TestCase
{
    /** @var SecretGenerator|MockObject */
    protected $secretGeneratorMock;

    /** @var SecretGeneratorReady */
    protected $sut;

    public function setUp()
    {
        $this->secretGeneratorMock = $this->getMockBuilder(SecretGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sut = new SecretGeneratorReady($this->secretGeneratorMock);
    }

    public function testGetSecretGenerator()
    {
        $actualResult = $this->sut->getSecretGenerator();

        $this->assertSame($this->secretGeneratorMock, $actualResult);
    }
}
