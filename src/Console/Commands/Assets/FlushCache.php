<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Assets;

use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class FlushCache extends Command
{
    const NAME = 'assets:flushcache';

    /** @var ICacheManager */
    protected $cacheManager;

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
        try {
            $this->cacheManager->flush();
            $response->writeln('<info>Assets are flushed</info>');
        } catch (\Exception $e) {
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));
        }
    }
}
