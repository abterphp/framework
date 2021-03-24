<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Filesystem;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Filesystem\FileFinder;
use AbterPhp\Framework\Filesystem\IFileFinder;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class FileFinderBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    protected ?array $assetPaths = null;

    /**
     * @return array
     */
    public function getAssetPaths(): array
    {
        global $abterModuleManager;

        if ($this->assetPaths !== null) {
            return $this->assetPaths;
        }

        $this->assetPaths = $abterModuleManager->getAssetsPaths() ?: [];

        return $this->assetPaths;
    }

    /**
     * @param string[] $assetPaths
     *
     * @return $this
     */
    public function setAssetPaths(array $assetPaths): self
    {
        $this->assetPaths = $assetPaths;

        return $this;
    }

    /**
     * @inheritdoc
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
    public function registerBindings(IContainer $container): void
    {
        $fileFinder = new FileFinder();

        $this->registerResourcePaths($fileFinder);

        $container->bindInstance(FileFinder::class, $fileFinder);
        $container->bindInstance(IFileFinder::class, $fileFinder);
    }

    /**
     * @param FileFinder $fileFinder
     */
    private function registerResourcePaths(FileFinder $fileFinder): void
    {
        foreach ($this->getAssetPaths() as $key => $paths) {
            foreach ($paths as $path) {
                if (!$path) {
                    continue;
                }
                $fileFinder->registerFilesystem(new Filesystem(new LocalFilesystemAdapter($path)), $key);
            }
        }

        $dirPublic = rtrim(Environment::getVar(Env::DIR_PUBLIC), DIRECTORY_SEPARATOR);

        $fileFinder->registerFilesystem(new Filesystem(new LocalFilesystemAdapter($dirPublic)));
    }
}
