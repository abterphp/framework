<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\Tag;
use InvalidArgumentException;

class Textarea extends Tag implements IElement
{
    public const CLASS_WYSIWYG = 'wysiwyg';

    protected const DEFAULT_TAG = Html5::TAG_TEXTAREA;

    protected const DEFAULT_ROW = '3';

    /**
     * Textarea constructor.
     *
     * @param string          $inputId
     * @param string          $name
     * @param string          $value
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        string $value = '',
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= new Attributes();
        if ($inputId) {
            $attributes->replaceItem(new Attribute(Html5::ATTR_ID, $inputId));
        }
        if (!$attributes->hasItem(Html5::ATTR_ROWS)) {
            $attributes->replaceItem(new Attribute(Html5::ATTR_ROWS, static::DEFAULT_ROW));
        }

        $attributes->replaceItem(new Attribute(Html5::ATTR_NAME, $name));

        parent::__construct(null, $intents, $attributes, $tag);

        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!$this->getAttributes()->hasItem(Html5::ATTR_NAME)) {
            return '';
        }

        $values = $this->getAttributes()->getItem(Html5::ATTR_NAME)->getValues();
        if (null === $values) {
            return '';
        }

        return implode(' ', $values);
    }

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): IElement
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException();
        }

        $this->forceGetAttribute(Html5::ATTR_VALUE)->set(htmlspecialchars($value));

        return $this;
    }

    /**
     * @return string|string[]
     */
    public function getValue()
    {
        return $this->getAttribute(Html5::ATTR_VALUE)->getValue();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $value = $this->getAttributes()->getItem(Html5::ATTR_VALUE)->getValue();

        $attributes = clone $this->getAttributes();
        $attributes->remove(Html5::ATTR_VALUE);

        return TagHelper::toString($this->tag, $value, $attributes);
    }
}
