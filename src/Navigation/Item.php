<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;

class Item extends Component
{
    const DEFAULT_TAG = Html5::TAG_LI;

    const INTENT_DROPDOWN = 'dropdown';
}
