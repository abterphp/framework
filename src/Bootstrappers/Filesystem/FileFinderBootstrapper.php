<?php

namespace AbterPhp\Framework\Bootstrappers\Filesystem;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Filesystem\FileFinder;
use AbterPhp\Framework\Filesystem\IFileFinder;
use AbterPhp\Framework\Module\Manager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class FileFinderBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            FileFinder::class,
            IFileFinder::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $fileFinder = new FileFinder();

        $this->registerResourcePaths($fileFinder);

        $container->bindInstance(FileFinder::class, $fileFinder);
        $container->bindInstance(IFileFinder::class, $fileFinder);
    }

    /**
     * @param FileFinder $fileFinder
     */
    private function registerResourcePaths(FileFinder $fileFinder)
    {
        /** @var Manager $abterModuleManager */
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
}
