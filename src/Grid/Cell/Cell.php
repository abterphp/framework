<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\INode;

class Cell extends Component implements ICell
{
    protected const DEFAULT_TAG = Html5::TAG_TD;

    public const INTENT_ACTIONS = 'actions';

    public const GROUP_ACTIONS = 'actions';

    protected string $group = '';

    /**
     * Cell constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string                    $group
     * @param string[]                  $intents
     * @param Attributes|null           $attributes
     * @param string|null               $tag
     */
    public function __construct(
        $content,
        string $group,
        array $intents = [],
        ?Attributes $attributes = null,
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
