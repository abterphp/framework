<?php

namespace AbterPhp\Framework\Bootstrappers\Console\Commands\Security;

use AbterPhp\Framework\Console\Commands\Security\SecretGenerator;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class SecretGeneratorBootstrapperTest extends TestCase
{
    /** @var SecretGeneratorBootstrapper */
    protected SecretGeneratorBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new SecretGeneratorBootstrapper();
    }

    public function testRegisterBindings()
    {
        $eventDispatchedMock = $this->getMockBuilder(IEventDispatcher::class)->getMock();
        $eventDispatchedMock->expects($this->once())->method('dispatch');

        $container = new Container();
        $container->bindInstance(IEventDispatcher::class, $eventDispatchedMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(SecretGenerator::class);
        $this->assertInstanceOf(SecretGenerator::class, $actual);
    }
}
