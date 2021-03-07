<?php

namespace AbterPhp\Framework\Bootstrappers\Dashboard;

use AbterPhp\Framework\Dashboard\Dashboard;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class DashboardBootstrapperTest extends TestCase
{
    /** @var DashboardBootstrapper */
    protected DashboardBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new DashboardBootstrapper();
    }

    public function testRegisterBindings()
    {
        $eventDispatcherMock = $this->getMockBuilder(IEventDispatcher::class)->disableOriginalConstructor()->getMock();
        $eventDispatcherMock->expects($this->once())->method('dispatch');

        $container = new Container();
        $container->bindInstance(IEventDispatcher::class, $eventDispatcherMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Dashboard::class);
        $this->assertInstanceOf(Dashboard::class, $actual);
    }
}
