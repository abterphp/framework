<?php

namespace AbterPhp\Framework\Bootstrappers\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Http\Middleware\Security;
use Opulence\Cache\ICacheBridge;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class SecurityBootstrapperTest extends TestCase
{
    /** @var SecurityBootstrapper - System Under Test */
    protected SecurityBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new SecurityBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::ENV_NAME, 'foo');

        $cacheBridgeMock = $this->getMockBuilder(ICacheBridge::class)->getMock();

        $container = new Container();
        $container->bindInstance(ICacheBridge::class, $cacheBridgeMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Security::class);
        $this->assertInstanceOf(Security::class, $actual);
    }
}
