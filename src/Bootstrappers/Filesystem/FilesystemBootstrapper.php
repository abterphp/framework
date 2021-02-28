<?php

namespace AbterPhp\Framework\Bootstrappers\Filesystem;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Filesystem\Uploader;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class FilesystemBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            Filesystem::class,
            Uploader::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $dirPrivate        = Environment::getVar(Env::DIR_PRIVATE);
        $adapter           = new LocalFilesystemAdapter($dirPrivate);
        $storedFileManager = new Filesystem($adapter);
        $container->bindInstance(Filesystem::class, $storedFileManager);

        $uploader = new Uploader($storedFileManager, $dirPrivate);
        $container->bindInstance(Uploader::class, $uploader);
    }
}
