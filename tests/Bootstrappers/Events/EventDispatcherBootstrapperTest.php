<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Events;

use Opulence\Events\Dispatchers\EventRegistry;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class EventDispatcherBootstrapperTest extends TestCase
{
    /** @var EventDispatcherBootstrapper - System Under Test */
    protected EventDispatcherBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new EventDispatcherBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $listenerName = 'foo';
        $events1      = [$listenerName => [fn () => null]];
        $events2      = [$listenerName => [fn () => null]];

        $this->sut->setBaseEvents($events1);
        $this->sut->setModuleEvents($events2);

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(IEventDispatcher::class);
        $this->assertInstanceOf(IEventDispatcher::class, $actual);

        /** @var EventRegistry $actual */
        $actual = $container->resolve(EventRegistry::class);
        $this->assertInstanceOf(EventRegistry::class, $actual);
        $this->assertCount(2, $actual->getListeners($listenerName));
    }

    public function testRegisterBindingsMixed(): void
    {
        $listenerName1 = 'foo';
        $events1       = [$listenerName1 => [fn () => null]];

        $listenerName2 = 'bar';
        $events2       = [$listenerName2 => [fn () => null]];

        $this->sut->setBaseEvents($events1);
        $this->sut->setModuleEvents($events2);

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(IEventDispatcher::class);
        $this->assertInstanceOf(IEventDispatcher::class, $actual);

        /** @var EventRegistry $actual */
        $actual = $container->resolve(EventRegistry::class);
        $this->assertInstanceOf(EventRegistry::class, $actual);
        $this->assertCount(1, $actual->getListeners($listenerName1));
        $this->assertCount(1, $actual->getListeners($listenerName2));
    }
}
