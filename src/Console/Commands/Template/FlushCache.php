<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Template;

use AbterPhp\Framework\Template\CacheManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class FlushCache extends Command
{
    public const NAME = 'template:flushcache';

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
    protected function define()
    {
        $this->setName(static::NAME)
            ->setDescription('Flushes templates')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $this->cacheManager->flush();
        $response->writeln('<info>Template cache is flushed</info>');
    }
}
