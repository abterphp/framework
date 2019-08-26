<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Assets;

use AbterPhp\Framework\Assets\CacheManager\Dummy as DummyCacheManager;
use AbterPhp\Framework\Assets\CacheManager\Flysystem as FlysystemCacheManager;
use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Constant\Env;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class CacheManagerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            DummyCacheManager::class,
            FlysystemCacheManager::class,
            ICacheManager::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        if (Environment::getVar(Env::ENV_NAME) === Environment::DEVELOPMENT) {
            $cacheManager = new DummyCacheManager();
        } else {
            $cacheManager = new FlysystemCacheManager();

            $this->registerCachePaths($cacheManager);
        }

        $container->bindInstance(ICacheManager::class, $cacheManager);
    }

    /**
     * @param ICacheManager $cacheManager
     */
    private function registerCachePaths(ICacheManager $cacheManager)
    {
        $dirPublic = rtrim(Environment::getVar(Env::DIR_PUBLIC), DIRECTORY_SEPARATOR);

        $cacheManager->registerFilesystem(new Filesystem(new Local($dirPublic)));
    }
}
