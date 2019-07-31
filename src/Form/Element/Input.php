<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\Tag;

class Input extends Tag implements IElement
{
    const DEFAULT_TAG = Html5::TAG_INPUT;

    const DEFAULT_TYPE = self::TYPE_TEXT;

    const TYPE_BUTTON         = 'button';
    const TYPE_CHECKBOX       = 'checkbox';
    const TYPE_COLOR          = 'color';
    const TYPE_DATE           = 'date';
    const TYPE_DATETIME       = 'datetime';
    const TYPE_DATETIME_LOCAL = 'datetime-local';
    const TYPE_EMAIL          = 'email';
    const TYPE_FILE           = 'file';
    const TYPE_HIDDEN         = 'hidden';
    const TYPE_IMAGE          = 'image';
    const TYPE_MONTH          = 'month';
    const TYPE_NUMBER         = 'number';
    const TYPE_PASSWORD       = 'password';
    const TYPE_RADIO          = 'radio';
    const TYPE_RANGE          = 'range';
    const TYPE_RESET          = 'reset';
    const TYPE_SEARCH         = 'search';
    const TYPE_SUBMIT         = 'submit';
    const TYPE_TEL            = 'tel';
    const TYPE_TEXT           = 'text';
    const TYPE_URL            = 'url';
    const TYPE_WEEK           = 'week';

    const NAME_HTTP_METHOD = '_method';

    const AUTOCOMPLETE_OFF = 'off';

    /**
     * Input constructor.
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
        if (!array_key_exists(Html5::ATTR_TYPE, $attributes)) {
            $attributes[Html5::ATTR_TYPE] = static::DEFAULT_TYPE;
        }

        $attributes[Html5::ATTR_NAME] = $name;

        parent::__construct(null, $intents, $attributes, $tag);

        $this->setValue($value);
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
        return StringHelper::createTag($this->tag, $this->attributes);
    }
}
