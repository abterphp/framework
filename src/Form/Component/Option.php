<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Option extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_OPTION;

    protected const PROTECTED_KEYS = [Html5::ATTR_VALUE];

    /**
     * Option constructor.
     *
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param string[]                  $intents
     * @param array<string,Attribute>|null           $counterAttributes
     * @param string|null               $tag
     */
    public function __construct(
        string $value,
        $content,
        bool $isSelected = false,
        array $intents = [],
        ?array $counterAttributes = null,
        ?string $tag = null
    ) {
        $counterAttributes ??= [];
        $counterAttributes[Html5::ATTR_VALUE] = new Attribute(Html5::ATTR_VALUE, $value);

        if ($isSelected) {
            $counterAttributes[Html5::ATTR_SELECTED] = new Attribute(Html5::ATTR_SELECTED);
        }

        parent::__construct($content, $intents, $counterAttributes, $tag);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->attributes[Html5::ATTR_VALUE]->getValue();
    }
}
