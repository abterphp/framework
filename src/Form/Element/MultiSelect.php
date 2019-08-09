<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;

class MultiSelect extends Select
{
    /**
     * Select constructor.
     *
     * @param string      $inputId
     * @param string      $name
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        $attributes[Html5::ATTR_MULTIPLE] = null;

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
