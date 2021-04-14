<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Dashboard;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;

class Dashboard extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;

    protected const CLASS_DASHBOARD = 'dashboard-container';

    public function __construct(
        $content = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= new Attributes();
        $attributes->mergeItem(new Attribute(Html5::ATTR_CLASS, self::CLASS_DASHBOARD));

        parent::__construct($content, $intents, $attributes, $tag);
    }
}
