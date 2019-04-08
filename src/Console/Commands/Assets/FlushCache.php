<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Assets;

use AbterPhp\Framework\Assets\AssetManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class FlushCache extends Command
{
    const NAME = 'assets:flushcache';

    /** @var AssetManager */
    protected $assets;

    /**
     * FlushCacheCommand constructor.
     *
     * @param AssetManager $assets
     */
    public function __construct(AssetManager $assets)
    {
        $this->assets = $assets;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName(static::NAME)
            ->setDescription('Flushes assets cache')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $this->emptyDirectory($this->assets->getDirCacheCss());
        $response->writeln('<info>CSS assets are flushed</info>');

        $this->emptyDirectory($this->assets->getDirCacheJs());
        $response->writeln('<info>JS assets are flushed</info>');
    }

    /**
     * @param string $path
     * @param bool   $deleteDir
     */
    private function emptyDirectory(string $path, bool $deleteDir = false)
    {
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                $this->emptyDirectory($fileInfo->getRealPath(), true);
                if ($deleteDir) {
                    unlink($fileInfo->getRealPath());
                }
            } elseif ($fileInfo->isFile()) {
                unlink($fileInfo->getRealPath());
            }
        }
    }
}
