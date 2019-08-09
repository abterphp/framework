<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\INode;

class Option extends Component
{
    const DEFAULT_TAG = Html5::TAG_OPTION;

    /**
     * Option constructor.
     *
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param string[]                  $intents
     * @param array                     $attributes
     * @param string|null               $tag
     */
    public function __construct(
        string $value,
        $content,
        bool $isSelected = false,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        $attributes[Html5::ATTR_VALUE] = $value;

        if ($isSelected) {
            $attributes[Html5::ATTR_SELECTED] = null;
        }

        parent::__construct($content, $intents, $attributes, $tag);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        if (!$this->hasAttribute(Html5::ATTR_VALUE)) {
            return '';
        }

        $value = $this->getAttribute(Html5::ATTR_VALUE);
        if (null === $value) {
            return '';
        }

        return (string)$value;
    }
}
