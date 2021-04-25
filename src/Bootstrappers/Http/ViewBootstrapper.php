<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Config\Config;
use Opulence\Framework\Views\Bootstrappers\ViewBootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Caching\ArrayCache;
use Opulence\Views\Caching\FileCache;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Factories\IO\IViewNameResolver;
use Opulence\Views\Factories\IO\IViewReader;
use Opulence\Views\Factories\IO\FileViewNameResolver;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\Factories\ViewFactory;

/**
 * Defines the view bootstrapper
 */
class ViewBootstrapper extends BaseBootstrapper
{
    protected const VIEWS_PATH = 'views/';

    protected ?array $resourcePaths = null;

    /**
     * @return array
     */
    public function getResourcePaths(): array
    {
        global $abterModuleManager;

        if ($this->resourcePaths !== null) {
            return $this->resourcePaths;
        }

        $paths = $abterModuleManager->getResourcePaths() ?: [];

        $this->resourcePaths = $paths;

        return $paths;
    }

    /**
     * @param array $resourcePaths
     *
     * @return $this
     */
    public function setResourcePaths(array $resourcePaths): self
    {
        $this->resourcePaths = $resourcePaths;

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Gets the view cache
     * To use a different view cache than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     *
     * @return ICache The view cache
     */
    protected function getViewCache(IContainer $container): ICache
    {
        switch (Config::mustGetString('views', 'cache')) {
            case ArrayCache::class:
                return new ArrayCache();
            default:
                return new FileCache(
                    Config::get('paths', 'views.compiled'),
                    Config::mustGetInt('views', 'cache.lifetime'),
                    Config::mustGetInt('views', 'gc.chance'),
                    Config::mustGetInt('views', 'gc.divisor')
                );
        }
    }

    /**
     * Gets the view view factory
     * To use a different view factory than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IViewFactory The view factory
     */
    protected function getViewFactory(IContainer $container): IViewFactory
    {
        $resolver = new FileViewNameResolver();
        $resolver->registerExtension('fortune');
        $resolver->registerExtension('fortune.php');
        $resolver->registerExtension('php');
        $viewReader = $this->getViewReader($container);
        $container->bindInstance(IViewNameResolver::class, $resolver);
        $container->bindInstance(IViewReader::class, $viewReader);

        $this->registerPaths($resolver);

        return new ViewFactory($resolver, $viewReader);
    }

    /**
     * @param FileViewNameResolver $resolver
     */
    protected function registerPaths(FileViewNameResolver $resolver): void
    {
        $globalPath = Config::mustGetString('paths', 'views.raw');
        if ($globalPath) {
            $resolver->registerPath($globalPath);
        }

        $resourcePaths = $this->getResourcePaths();

        $priority = count($resourcePaths);
        foreach ($resourcePaths as $path) {
            $modulePath = sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, static::VIEWS_PATH);

            if (!is_dir($modulePath)) {
                continue;
            }

            $resolver->registerPath($modulePath, $priority--);
        }
    }
}
