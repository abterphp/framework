<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Dashboard;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Dashboard extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;

    protected const CLASS_DASHBOARD = 'dashboard-container';

    /**
     * Dashboard constructor.
     *
     * @param array<string|INode>|string|INode|null $content
     * @param array                                 $intents
     * @param array<string,Attribute>|null          $attributes
     * @param string|null                           $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct($content, $intents, $attributes, $tag);

        $this->appendToClass(self::CLASS_DASHBOARD);
    }
}
