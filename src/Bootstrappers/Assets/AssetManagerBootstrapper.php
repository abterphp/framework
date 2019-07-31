<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Assets;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Assets\CacheManager\Dummy as DummyCacheManager;
use AbterPhp\Framework\Assets\CacheManager\Flysystem as FlysystemCacheManager;
use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Filesystem\FileFinder;
use AbterPhp\Framework\Filesystem\IFileFinder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class AssetManagerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            AssetManager::class,
            FileFinder::class,
            DummyCacheManager::class,
            FlysystemCacheManager::class,
            ICacheManager::class,
            IFileFinder::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $this->registerAssets($container);
        $this->registerViewFunction($container);
    }

    /**
     * @param IContainer $container
     */
    private function registerAssets(IContainer $container)
    {
        $minifierFactory = new MinifierFactory();
        $fileFinder      = new FileFinder();
        if (Environment::getVar(Env::ENV_NAME) === Environment::DEVELOPMENT) {
            $cacheManager = new DummyCacheManager();
        } else {
            $cacheManager = new FlysystemCacheManager();
        }

        $this->registerResourcePaths($fileFinder);
        $this->registerCachePaths($cacheManager);

        $assetManager = new AssetManager($minifierFactory, $fileFinder, $cacheManager);

        $container->bindInstance(FileFinder::class, $fileFinder);
        $container->bindInstance(IFileFinder::class, $fileFinder);
        $container->bindInstance(ICacheManager::class, $cacheManager);
        $container->bindInstance(AssetManager::class, $assetManager);
    }

    /**
     * @param ICacheManager $cacheManager
     */
    private function registerCachePaths(ICacheManager $cacheManager)
    {
        $dirPublic = rtrim(getenv(Env::DIR_PUBLIC), DIRECTORY_SEPARATOR);

        $cacheManager->registerFilesystem(new Filesystem(new Local($dirPublic)));
    }

    /**
     * @param FileFinder $fileFinder
     */
    private function registerResourcePaths(FileFinder $fileFinder)
    {
        global $abterModuleManager;

        $assetsPaths = $abterModuleManager->getAssetsPaths();

        foreach ($assetsPaths as $key => $paths) {
            foreach ($paths as $path) {
                if (!$path) {
                    continue;
                }
                $fileFinder->registerFilesystem(new Filesystem(new Local($path)), $key);
            }
        }

        $dirPublic = rtrim(getenv(Env::DIR_PUBLIC), DIRECTORY_SEPARATOR);

        $fileFinder->registerFilesystem(new Filesystem(new Local($dirPublic)));
    }

    /**
     * @param IContainer $container
     */
    private function registerViewFunction(IContainer $container)
    {
        /** @var AssetManager $assets */
        $assets = $container->resolve(AssetManager::class);

        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);
        $transpiler->registerViewFunction(
            'assetJs',
            function ($keys, $type = '') use ($assets) {
                $callback = function ($key) use ($assets, $type) {
                    $path = $assets->ensureJsWebPath($key);
                    if (empty($path)) {
                        return '';
                    }

                    if ($type) {
                        return sprintf('<script type="%s" src="%s"></script>', $type, $path) . PHP_EOL;
                    }

                    return sprintf('<script src="%s"></script>', $path) . PHP_EOL;
                };

                return implode(PHP_EOL, array_map($callback, (array)$keys));
            }
        );
        $transpiler->registerViewFunction(
            'assetCss',
            function ($keys) use ($assets) {
                $callback = function ($key) use ($assets) {
                    $path = $assets->ensureCssWebPath($key);
                    if (empty($path)) {
                        return '';
                    }

                    return sprintf('<link href="%s" rel="stylesheet">', $path) . PHP_EOL;
                };

                return implode("\n", array_map($callback, (array)$keys));
            }
        );
        $transpiler->registerViewFunction(
            'assetImg',
            function ($key, $alt = '') use ($assets) {
                $path = $assets->ensureImgWebPath($key);
                if (empty($path)) {
                    return '';
                }

                return sprintf('<img src="%s" alt="%s">', $path, $alt) . PHP_EOL;
            }
        );
    }
}
