<?php

namespace AbterPhp\Framework\Bootstrappers\Http\Views;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Ioc\Container;
use Opulence\Views\Factories\IViewFactory;
use PHPUnit\Framework\TestCase;

class BuildersBootstrapperTest extends TestCase
{
    /** @var BuildersBootstrapper */
    protected BuildersBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new BuildersBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::ENV_NAME, 'foo');

        $viewFactoryMock = $this->getMockBuilder(IViewFactory::class)->getMock();
        $viewFactoryMock->expects($this->once())->method('registerBuilder');

        $container = new Container();
        $container->bindInstance(IViewFactory::class, $viewFactoryMock);

        $this->sut->registerBindings($container);
    }
}
