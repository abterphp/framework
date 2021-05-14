<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\Helper\Tag as TagHelper;
use AbterPhp\Framework\Html\Tag;

class Textarea extends Input implements IElement
{
    public const CLASS_WYSIWYG = 'wysiwyg';

    protected const DEFAULT_TAG = Html5::TAG_TEXTAREA;

    protected const DEFAULT_ROW = '3';

    protected const PROTECTED_KEYS = [Html5::ATTR_ID, Html5::ATTR_NAME, Html5::ATTR_ROWS, Html5::ATTR_VALUE];

    /**
     * Textarea constructor.
     *
     * @param string                        $inputId
     * @param string                        $name
     * @param string                        $value
     * @param string[]                      $intents
     * @param array<string, Attribute>|null $attributes
     * @param string|null                   $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        string $value = '',
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $attributes                 ??= [];
        $attributes = Attributes::addItem($attributes, Html5::ATTR_ID, $inputId);
        if (!in_array(Html5::ATTR_ROWS, $attributes)) {
            $attributes = Attributes::addItem($attributes, Html5::ATTR_ROWS, static::DEFAULT_ROW);
        }

        $attributes = Attributes::addItem($attributes, Html5::ATTR_NAME, $name);

        Tag::__construct(null, $intents, $attributes, $tag);

        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $value = $this->attributes[Html5::ATTR_VALUE]->getValue();

        $attributes = $this->attributes;
        unset($attributes[Html5::ATTR_VALUE]);

        return TagHelper::toString($this->tag, $value, $attributes);
    }
}
