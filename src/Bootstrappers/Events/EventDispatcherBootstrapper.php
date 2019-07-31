<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Events;

use AbterPhp\Framework\Module\Manager;// @phan-suppress-current-line PhanUnreferencedUseNormal
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Events\Bootstrappers\EventDispatcherBootstrapper as BaseBootstrapper;

/**
 * Defines the event dispatcher bootstrapper
 */
class EventDispatcherBootstrapper extends BaseBootstrapper
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * Gets the list of event names to the list of listeners, which can be callables or "className@classMethod" strings
     *
     * @return array The event listener config
     */
    protected function getEventListenerConfig(): array
    {
        /** @var Manager $abterModuleManager */
        global $abterModuleManager;

        $allEvents = require Config::get('paths', 'config') . '/events.php';

        foreach ($abterModuleManager->getEvents() as $type => $events) {
            if (empty($events)) {
                throw new \RuntimeException(sprintf('Invalid events: %s - %s', $type, json_encode($events)));
            }
            if (!isset($allEvents[$type])) {
                $allEvents[$type] = [];
            }
            $allEvents[$type] = array_merge($allEvents[$type], $events);
        }

        return $allEvents;
    }
}
