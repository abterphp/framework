<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\Helper\Tag as TagHelper;
use AbterPhp\Framework\Html\IStringer;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;
use LogicException;

class Select extends Input
{
    protected const ERROR_NO_CONTENT = 'Select can not contain nodes';

    protected const SEPARATOR = "\n";

    protected const DEFAULT_TAG  = Html5::TAG_SELECT;
    protected const CONTENT_TYPE = Option::class;

    protected const PROTECTED_KEYS = [Html5::ATTR_ID, Html5::ATTR_NAME];

    /** @var Option[] */
    protected array $content = [];

    /**
     * Select constructor.
     *
     * @param string                        $inputId
     * @param string                        $name
     * @param string[]                      $intents
     * @param array<string, Attribute>|null $attributes
     * @param string|null                   $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= [];
        $attributes = Attributes::addItem($attributes, Html5::ATTR_ID, $inputId);
        $attributes = Attributes::addItem($attributes, Html5::ATTR_NAME, $name);

        Tag::__construct(null, $intents, $attributes, $tag);
    }

    /**
     * @param array<string|IStringer>|string|IStringer|null $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        if (null === $content) {
            return $this;
        }

        throw new LogicException(static::ERROR_NO_CONTENT);
    }

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): self
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
    protected function setValueInner(array $values): self
    {
        foreach ($this->content as $option) {
            $value = $option->getValue();
            if (in_array($value, $values, true)) {
                $option->setAttribute(new Attribute(Html5::ATTR_SELECTED));
            } elseif ($option->hasAttribute(Html5::ATTR_SELECTED)) {
                $option->removeAttribute(Html5::ATTR_SELECTED);
            }
        }

        return $this;
    }

    /**
     * @suppress PhanParamSignatureMismatch
     *
     * @return string|null
     */
    public function getValue()
    {
        foreach ($this->content as $option) {
            if ($option->hasAttribute(Html5::ATTR_SELECTED)) {
                return $option->getValue();
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes[Html5::ATTR_NAME]->getValue();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = Node::__toString();

        return TagHelper::toString($this->tag, $content, $this->getAttributes());
    }
}
