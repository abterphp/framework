<?php

namespace AbterPhp\Framework\Bootstrappers\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Http\Middleware\EnvironmentWarning;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class EnvironmentWarningBootstrapperTest extends TestCase
{
    /** @var EnvironmentWarningBootstrapper */
    protected EnvironmentWarningBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new EnvironmentWarningBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::ENV_NAME, 'foo');

        $translatorMock = $this->getMockBuilder(ITranslator::class)->getMock();

        $container = new Container();
        $container->bindInstance(ITranslator::class, $translatorMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(EnvironmentWarning::class);
        $this->assertInstanceOf(EnvironmentWarning::class, $actual);
    }
}
