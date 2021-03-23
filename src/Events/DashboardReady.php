<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Dashboard\Dashboard;

class DashboardReady
{
    private Dashboard $dashboard;

    /**
     * DashboardReady constructor.
     *
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * @return Dashboard
     */
    public function getDashboard(): Dashboard
    {
        return $this->dashboard;
    }
}
