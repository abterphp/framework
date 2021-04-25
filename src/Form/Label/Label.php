<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Label extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_LABEL;

    public const INTENT_TOGGLE = 'toggle';

    protected string $template;

    /**
     * Label constructor.
     *
     * @param string                       $inputId
     * @param INode[]|INode|string|null    $content
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(
        string $inputId,
        $content = null,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= [];
        $attributes[Html5::ATTR_FOR] = new Attribute(Html5::ATTR_FOR, $inputId);

        parent::__construct($content, $intents, $attributes, $tag);
    }
}
