<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Dashboard;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Dashboard\Dashboard;
use AbterPhp\Framework\Events\DashboardReady;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class DashboardBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            Dashboard::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        $dashboard = new Dashboard();

        $eventDispatcher = $container->resolve(IEventDispatcher::class);
        $eventDispatcher->dispatch(Event::DASHBOARD_READY, new DashboardReady($dashboard));

        $container->bindInstance(Dashboard::class, $dashboard);
    }
}
