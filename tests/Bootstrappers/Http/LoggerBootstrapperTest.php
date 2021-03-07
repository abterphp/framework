<?php

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerBootstrapperTest extends TestCase
{
    /** @var LoggerBootstrapper */
    protected LoggerBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new LoggerBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::DIR_LOGS);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::DIR_LOGS, '/tmp/baz');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(LoggerInterface::class);
        $this->assertInstanceOf(LoggerInterface::class, $actual);
    }
}
