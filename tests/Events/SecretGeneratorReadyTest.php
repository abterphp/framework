<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Console\Commands\Security\SecretGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SecretGeneratorReadyTest extends TestCase
{
    /** @var SecretGeneratorReady - System Under Test */
    protected SecretGeneratorReady $sut;

    /** @var SecretGenerator|MockObject */
    protected $secretGeneratorMock;

    public function setUp(): void
    {
        $this->secretGeneratorMock = $this->createMock(SecretGenerator::class);

        $this->sut = new SecretGeneratorReady($this->secretGeneratorMock);

        parent::setUp();
    }

    public function testGetSecretGenerator()
    {
        $actualResult = $this->sut->getSecretGenerator();

        $this->assertSame($this->secretGeneratorMock, $actualResult);
    }
}
