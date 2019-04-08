<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Console\Commands\Cache\FlushCache;

class FlushCommandReady
{
    /** @var FlushCache */
    private $flushCache;

    /**
     * FlushCommandReady constructor.
     *
     * @param FlushCache $flushCache
     */
    public function __construct(FlushCache $flushCache)
    {
        $this->flushCache = $flushCache;
    }

    /**
     * @return FlushCache
     */
    public function getFlushCache(): FlushCache
    {
        return $this->flushCache;
    }
}
