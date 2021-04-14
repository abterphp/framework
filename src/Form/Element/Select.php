<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;

class Select extends Component implements IElement
{
    protected const DEFAULT_TAG = Html5::TAG_SELECT;

    /** @var Option[] */
    protected array $nodes = [];

    protected string $nodeClass = Option::class;

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
        if ($inputId) {
            $attributes->replaceItem(new Attribute(Html5::ATTR_ID, $inputId));
        }
        $attributes->replaceItem(new Attribute(Html5::ATTR_NAME, $name));

        parent::__construct(null, $intents, $attributes, $tag);
    }

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): IElement
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException();
        }

        return $this->setValueInner([$value]);
    }

    /**
     * @return string[]|null
     */
    public function getValue()
    {
        return $this->getAttribute(Html5::ATTR_VALUE)->getValues();
    }

    /**
     * @param string[] $values
     *
     * @return $this
     */
    protected function setValueInner(array $values): IElement
    {
        foreach ($this->nodes as $option) {
            if (in_array($option->getValue(), $values, true)) {
                $option->getAttributes()->replaceItem(new Attribute(Html5::ATTR_SELECTED));
            } elseif ($option->getAttributes()->hasItem(Html5::ATTR_SELECTED)) {
                $option->getAttributes()->remove(Html5::ATTR_SELECTED);
            }
        }

        return $this;
    }

    /**
     * @return string
     */

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->forceGetAttribute(Html5::ATTR_NAME)->getValue();
    }
}
