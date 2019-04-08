<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Assets;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use AbterPhp\Framework\Constant\Env;
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
        $factory = new MinifierFactory();

        $isProduction = getenv(Env::ENV_NAME) === Environment::PRODUCTION;

        $dirPublic = rtrim(getenv(Env::DIR_PUBLIC), DIRECTORY_SEPARATOR);
        $dirTmp    = $dirPublic . DIRECTORY_SEPARATOR . 'tmp';

        $dirRootJs  = getenv(Env::DIR_ROOT_JS) ?: $dirPublic;
        $dirRootCss = getenv(Env::DIR_ROOT_CSS) ?: $dirPublic;

        $dirCacheJs  = getenv(Env::DIR_CACHE_JS) ?: $dirTmp;
        $dirCacheCss = getenv(Env::DIR_CACHE_CSS) ?: $dirTmp;

        $pathCacheJs  = getenv(Env::PATH_CACHE_JS) ?: \mb_substr($dirTmp, \mb_strlen($dirRootJs));
        $pathCacheCss = getenv(Env::PATH_CACHE_CSS) ?: \mb_substr($dirTmp, \mb_strlen($dirRootCss));

        $purifier = new AssetManager(
            $factory,
            $dirRootJs,
            $dirRootCss,
            $dirCacheJs,
            $dirCacheCss,
            $pathCacheJs,
            $pathCacheCss,
            $isProduction
        );

        $container->bindInstance(AssetManager::class, $purifier);
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
    }
}
