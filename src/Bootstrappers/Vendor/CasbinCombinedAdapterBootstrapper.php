<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Authorization\CacheManager;
use AbterPhp\Framework\Authorization\CombinedAdapter;
use CasbinAdapter\Database\Adapter;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class CasbinCombinedAdapterBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            CombinedAdapter::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        $cacheManager    = $container->resolve(CacheManager::class);
        $databaseAdapter = $container->resolve(Adapter::class);

        $combinedAdapter = new CombinedAdapter($databaseAdapter, $cacheManager);

        $container->bindInstance(CombinedAdapter::class, $combinedAdapter);
    }
}
