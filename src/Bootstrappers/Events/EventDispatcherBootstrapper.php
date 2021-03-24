<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Events;

use Opulence\Events\Dispatchers\EventRegistry;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Events\Dispatchers\SynchronousEventDispatcher;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Events\Bootstrappers\EventDispatcherBootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

/**
 * Defines the event dispatcher bootstrapper
 */
class EventDispatcherBootstrapper extends BaseBootstrapper
{
    protected ?array $baseEvents   = null;
    protected ?array $moduleEvents = null;

    /**
     * @return array
     */
    protected function getBaseEvents(): array
    {
        if ($this->baseEvents === null) {
            $this->baseEvents = require Config::get('paths', 'config') . '/events.php';
        }

        return $this->baseEvents;
    }

    /**
     * @param array $baseEvents
     *
     * @return $this
     */
    public function setBaseEvents(array $baseEvents): self
    {
        $this->baseEvents = $baseEvents;

        return $this;
    }

    /**
     * @return array
     */
    public function getModuleEvents(): array
    {
        global $abterModuleManager;

        if ($this->moduleEvents !== null) {
            return $this->moduleEvents;
        }

        $this->moduleEvents = $abterModuleManager->getEvents() ?: [];

        return $this->moduleEvents;
    }

    /**
     * @param array $moduleEvents
     *
     * @return $this
     */
    public function setModuleEvents(array $moduleEvents): self
    {
        $this->moduleEvents = $moduleEvents;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $eventRegistry = new EventRegistry();
        $container->bindInstance(EventRegistry::class, $eventRegistry);

        $eventDispatcher = $this->getEventDispatcher($container);
        $container->bindInstance(IEventDispatcher::class, $eventDispatcher);
    }

    /**
     * Gets the event dispatcher
     *
     * @param IContainer $container The IoC container
     *
     * @return IEventDispatcher The event dispatcher
     * @throws IocException
     */
    protected function getEventDispatcher(IContainer $container): IEventDispatcher
    {
        /** @var EventRegistry $eventRegistry */
        $eventRegistry = $container->resolve(EventRegistry::class);

        foreach ($this->getEventListenerConfig() as $eventName => $listeners) {
            foreach ((array)$listeners as $listener) {
                $eventRegistry->registerListener($eventName, $this->getEventListenerCallback($listener, $container));
            }
        }

        return new SynchronousEventDispatcher($eventRegistry);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * Gets the list of event names to the list of listeners, which can be callables or "className@classMethod" strings
     *
     * @return array The event listener config
     */
    protected function getEventListenerConfig(): array
    {
        $allEvents = $this->getBaseEvents();

        foreach ($this->getModuleEvents() as $type => $events) {
            assert(is_array($events) && count($events) > 0, sprintf('invalid events for "%s"', $type));

            if (!isset($allEvents[$type])) {
                $allEvents[$type] = [];
            }

            $allEvents[$type] = array_merge($allEvents[$type], $events);
        }

        return $allEvents;
    }
}
