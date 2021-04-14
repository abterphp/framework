<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;

class MultiSelect extends Select
{
    /**
     * Select constructor.
     *
     * @param string          $inputId
     * @param string          $name
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= new Attributes();
        $attributes->replaceItem(new Attribute(Html5::ATTR_MULTIPLE));

        parent::__construct($inputId, $name, $intents, $attributes, $tag);
    }

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): IElement
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException();
        }

        foreach ($value as $v) {
            if (!is_string($v)) {
                throw new \InvalidArgumentException();
            }
        }

        return $this->setValueInner($value);
    }
}
