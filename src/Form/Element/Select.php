<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
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
        if ($inputId) {
            $attributes[Html5::ATTR_ID] = $inputId;
        }
        $attributes[Html5::ATTR_NAME] = $name;

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
     * @param string[] $values
     *
     * @return $this
     */
    public function setValueInner(array $values): IElement
    {
        foreach ($this->nodes as $option) {
            if (in_array($option->getValue(), $values, true)) {
                $option->setAttribute(Html5::ATTR_SELECTED, null);
            } elseif ($option->hasAttribute(Html5::ATTR_SELECTED)) {
                $option->unsetAttribute(Html5::ATTR_SELECTED);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!$this->hasAttribute(Html5::ATTR_NAME)) {
            return '';
        }

        $value = $this->getAttribute(Html5::ATTR_NAME);
        if (null === $value) {
            return '';
        }

        return $value;
    }
}
