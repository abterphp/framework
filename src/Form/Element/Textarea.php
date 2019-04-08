<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\Tag;

class Textarea extends Tag implements IElement
{
    const DEFAULT_TAG = Html5::TAG_TEXTAREA;

    const DEFAULT_ROW = '3';

    const CLASS_WYSIWYG = 'wysiwyg';

    /** @var array */
    protected $attributes = [];

    /**
     * Textarea constructor.
     *
     * @param string      $inputId
     * @param string      $name
     * @param string      $value
     * @param string[]    $intents
     * @param string[][]  $attributes
     * @param string|null $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        string $value = '',
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        if ($inputId) {
            $attributes[Html5::ATTR_ID] = $inputId;
        }
        if (!array_key_exists(Html5::ATTR_ROWS, $attributes)) {
            $attributes[Html5::ATTR_ROWS] = static::DEFAULT_ROW;
        }

        $attributes[Html5::ATTR_NAME]  = $name;
        $attributes[Html5::ATTR_VALUE] = htmlspecialchars($value);

        parent::__construct(null, $intents, $attributes, $tag);
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

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value): IElement
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException();
        }

        return $this->setAttribute(Html5::ATTR_VALUE, $value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $attributes = $this->attributes;
        $content    = $this->getAttribute(Html5::ATTR_VALUE);

        unset($attributes[Html5::ATTR_VALUE]);

        return StringHelper::wrapInTag($content, $this->tag, $attributes);
    }
}
