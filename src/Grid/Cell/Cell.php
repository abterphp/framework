<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Cell extends Tag implements ICell
{
    public const INTENT_ACTIONS = 'actions';

    public const GROUP_ACTIONS = 'actions';

    protected const DEFAULT_TAG = Html5::TAG_TD;

    protected string $group = '';

    /**
     * Cell constructor.
     *
     * @param INode[]|INode|string|null    $content
     * @param string                       $group
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(
        $content,
        string $group,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct($content, $intents, $attributes, $tag);

        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }
}
