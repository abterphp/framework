<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Navigation;

use AbterPhp\Framework\Constant\Navigation as NavConstant;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Navigation\Navigation;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class NavigationBootstrapperTest extends TestCase
{
    /** @var NavigationBootstrapper - System Under Test */
    protected NavigationBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new NavigationBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $translatorMock      = $this->getMockBuilder(ITranslator::class)->getMock();
        $eventDispatcherMock = $this->getMockBuilder(IEventDispatcher::class)->getMock();

        $container = new Container();
        $container->bindInstance(ITranslator::class, $translatorMock);
        $container->bindInstance(IEventDispatcher::class, $eventDispatcherMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(NavConstant::NAVBAR);
        $this->assertInstanceOf(Navigation::class, $actual);

        $actual = $container->resolve(NavConstant::PRIMARY);
        $this->assertInstanceOf(Navigation::class, $actual);
    }
}
