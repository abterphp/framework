<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\INode;

class Help extends Component
{
    public const CLASS_HELP_BLOCK = 'help-block';

    protected const DEFAULT_TAG = Html5::TAG_DIV;

    /**
     * Help constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param array                     $intents
     * @param Attributes|null           $attributes
     * @param string|null               $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= new Attributes();
        $attributes->mergeItem(new Attribute(Html5::ATTR_CLASS, self::CLASS_HELP_BLOCK));

        parent::__construct($content, $intents, $attributes, $tag);
    }
}
