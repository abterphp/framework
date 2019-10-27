<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Dashboard;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;

class Dashboard extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;

    protected const CLASS_DASHBOARD = 'dashboard-container';

    /** @var string[] */
    protected $attributes = [
        Html5::ATTR_CLASS => self::CLASS_DASHBOARD,
    ];
}
