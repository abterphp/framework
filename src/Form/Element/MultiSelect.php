<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;

class MultiSelect extends Select
{
    protected const ERROR_NO_CONTENT = 'MultiSelect can not contain nodes';

    /**
     * MultiSelect constructor.
     *
     * @param string           $inputId
     * @param string           $name
     * @param string[]         $intents
     * @param Attribute[]|null $attributes
     * @param string|null      $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= [];
        $attributes[Html5::ATTR_MULTIPLE] = new Attribute(Html5::ATTR_MULTIPLE);

        parent::__construct($inputId, $name, $intents, $attributes, $tag);
    }

    /**
     * @suppress PhanParamSignatureMismatch
     *
     * @return string[]
     */
    public function getValue()
    {
        $values = [];
        foreach ($this->content as $option) {
            if ($option->hasAttribute(Html5::ATTR_SELECTED)) {
                $values[] = $option->getValue();
            }
        }

        return $values;
    }

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): self
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
