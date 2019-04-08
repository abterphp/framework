<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Console\Commands\Cache;

use AbterPhp\Framework\Console\Commands\Cache\FlushCache;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Events\FlushCommandReady;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class FlushCacheBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [FlushCache::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $eventDispatcher = $container->resolve(IEventDispatcher::class);

        $command = new FlushCache();
        $eventDispatcher->dispatch(Event::FLUSH_COMMAND_READY, new FlushCommandReady($command));

        $container->bindInstance(FlushCache::class, $command);
    }
}
