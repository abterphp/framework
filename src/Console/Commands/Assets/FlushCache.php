<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Assets;

use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class FlushCache extends Command
{
    public const NAME = 'assets:flushcache';

    protected ICacheManager $cacheManager;

    /**
     * FlushCacheCommand constructor.
     *
     * @param ICacheManager $cacheManager
     */
    public function __construct(ICacheManager $cacheManager)
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
            ->setDescription('Flushes assets cache')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response): void
    {
        try {
            $this->cacheManager->flush();
            $response->writeln('<info>Assets are flushed</info>');
        } catch (\Exception $e) {
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));
        }
    }
}
