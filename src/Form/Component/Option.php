<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\INode;

class Option extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_OPTION;

    /**
     * Option constructor.
     *
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param string[]                  $intents
     * @param Attributes|null           $counterAttributes
     * @param string|null               $tag
     */
    public function __construct(
        string $value,
        $content,
        bool $isSelected = false,
        array $intents = [],
        ?Attributes $counterAttributes = null,
        ?string $tag = null
    ) {
        $counterAttributes ??= new Attributes();
        $counterAttributes->replaceItem(new Attribute(Html5::ATTR_VALUE, $value));

        if ($isSelected) {
            $counterAttributes->replaceItem(new Attribute(Html5::ATTR_SELECTED));
        }

        parent::__construct($content, $intents, $counterAttributes, $tag);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        if (!$this->getAttributes()->hasItem(Html5::ATTR_VALUE)) {
            return '';
        }

        return $this->getAttribute(Html5::ATTR_VALUE)->getValue();
    }
}
