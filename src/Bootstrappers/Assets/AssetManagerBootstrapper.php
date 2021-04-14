<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Assets;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use AbterPhp\Framework\Assets\UrlFixer;
use AbterPhp\Framework\Config\Routes;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Filesystem\FileFinder;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class AssetManagerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            AssetManager::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        $this->registerAssets($container);
        $this->registerViewFunction($container);
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    private function registerAssets(IContainer $container): void
    {
        /** @var FileFinder $fileFinder */
        $fileFinder = $container->resolve(FileFinder::class);

        /** @var ICacheManager $cacheManager */
        $cacheManager    = $container->resolve(ICacheManager::class);
        $minifierFactory = $container->resolve(MinifierFactory::class);
        $urlFixer        = $container->resolve(UrlFixer::class);
        $routesConfig    = $container->resolve(Routes::class);

        $this->registerCachePaths($cacheManager);

        $assetManager = new AssetManager($minifierFactory, $fileFinder, $cacheManager, $urlFixer, $routesConfig);

        $container->bindInstance(AssetManager::class, $assetManager);
    }

    /**
     * @param ICacheManager $cacheManager
     */
    private function registerCachePaths(ICacheManager $cacheManager): void
    {
        $cacheDir = sprintf(
            '%s%s%s',
            rtrim(Environment::mustGetVar(Env::DIR_MEDIA), DIRECTORY_SEPARATOR),
            DIRECTORY_SEPARATOR,
            ltrim(Environment::mustGetVar(Env::CACHE_BASE_PATH), DIRECTORY_SEPARATOR)
        );

        $cacheManager->registerFilesystem(new Filesystem(new LocalFilesystemAdapter($cacheDir)));
    }

    /**
     * @param IContainer $container
     */
    private function registerViewFunction(IContainer $container): void
    {
        /** @var AssetManager $assetManager */
        $assetManager = $container->resolve(AssetManager::class);

        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);
        $transpiler->registerViewFunction('assetJs', $this->createAssetJsViewFunction($assetManager));
        $transpiler->registerViewFunction('assetCss', $this->createAssetCssViewFunction($assetManager));
        $transpiler->registerViewFunction('assetImg', $this->createAssetImgViewFunction($assetManager));
    }

    /**
     * @param AssetManager $assetManager
     *
     * @return callable
     */
    public function createAssetJsViewFunction(AssetManager $assetManager): callable
    {
        return function ($keys, $type = '') use ($assetManager) {
            $callback = function ($key) use ($assetManager, $type) {
                $path = $assetManager->ensureJsWebPath($key);
                if (empty($path)) {
                    return '';
                }

                if ($type) {
                    return sprintf('<script type="%s" src="%s"></script>', $type, $path) . PHP_EOL;
                }

                return sprintf('<script src="%s"></script>', $path) . PHP_EOL;
            };

            return implode(PHP_EOL, array_filter(array_map($callback, (array)$keys)));
        };
    }

    /**
     * @param AssetManager $assetManager
     *
     * @return callable
     */
    public function createAssetCssViewFunction(AssetManager $assetManager): callable
    {
        return function ($keys) use ($assetManager) {
            $callback = function ($key) use ($assetManager) {
                $path = $assetManager->ensureCssWebPath($key);
                if (empty($path)) {
                    return '';
                }

                return sprintf('<link href="%s" rel="stylesheet">', $path) . PHP_EOL;
            };

            return implode("\n", array_filter(array_map($callback, (array)$keys)));
        };
    }

    /**
     * @param AssetManager $assetManager
     *
     * @return callable
     */
    public function createAssetImgViewFunction(AssetManager $assetManager): callable
    {
        return function ($key, $alt = '') use ($assetManager) {
            $path = $assetManager->ensureImgWebPath($key);
            if (empty($path)) {
                return '';
            }

            return sprintf('<img src="%s" alt="%s">', $path, $alt) . PHP_EOL;
        };
    }
}
