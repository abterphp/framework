<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Authorization;

use AbterPhp\Framework\Authorization\CacheManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class FlushCache extends Command
{
    public const NAME = 'auth:flushcache';

    protected CacheManager $cacheManager;

    /**
     * FlushCacheCommand constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        $this->setName(static::NAME)
            ->setDescription('Flushes authorization rules cache')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response): void
    {
        $this->cacheManager->clearAll();
        $response->writeln('<info>Authorization cache is flushed</info>');
    }
}
