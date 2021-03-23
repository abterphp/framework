<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Navigation\Navigation;

class NavigationReady
{
    private Navigation $navigation;

    /**
     * Navigation constructor.
     *
     * @param Navigation $navigation
     */
    public function __construct(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * @return Navigation
     */
    public function getNavigation(): Navigation
    {
        return $this->navigation;
    }
}
