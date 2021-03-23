<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Authorization\CombinedAdapter;

class AuthReady
{
    private CombinedAdapter $adapter;

    /**
     * AuthReady constructor.
     *
     * @param CombinedAdapter $adapter
     */
    public function __construct(CombinedAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return CombinedAdapter
     */
    public function getAdapter(): CombinedAdapter
    {
        return $this->adapter;
    }
}
