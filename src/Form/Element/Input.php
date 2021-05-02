<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Tag as TagHelper;
use AbterPhp\Framework\Html\Tag;

class Input extends Tag implements IElement
{
    public const TYPE_BUTTON         = 'button';
    public const TYPE_CHECKBOX       = 'checkbox';
    public const TYPE_COLOR          = 'color';
    public const TYPE_DATE           = 'date';
    public const TYPE_DATETIME       = 'datetime';
    public const TYPE_DATETIME_LOCAL = 'datetime-local';
    public const TYPE_EMAIL          = 'email';
    public const TYPE_FILE           = 'file';
    public const TYPE_HIDDEN         = 'hidden';
    public const TYPE_IMAGE          = 'image';
    public const TYPE_MONTH          = 'month';
    public const TYPE_NUMBER         = 'number';
    public const TYPE_PASSWORD       = 'password';
    public const TYPE_RADIO          = 'radio';
    public const TYPE_RANGE          = 'range';
    public const TYPE_RESET          = 'reset';
    public const TYPE_SEARCH         = 'search';
    public const TYPE_SUBMIT         = 'submit';
    public const TYPE_TEL            = 'tel';
    public const TYPE_TEXT           = 'text';
    public const TYPE_URL            = 'url';
    public const TYPE_WEEK           = 'week';

    public const NAME_HTTP_METHOD = '_method';

    public const AUTOCOMPLETE_OFF = 'off';

    protected const DEFAULT_TAG = Html5::TAG_INPUT;

    protected const DEFAULT_TYPE = self::TYPE_TEXT;

    protected const PROTECTED_KEYS = [Html5::ATTR_ID, Html5::ATTR_TYPE, Html5::ATTR_NAME, Html5::ATTR_VALUE];

    /**
     * Input constructor.
     *
     * @param string           $inputId
     * @param string           $name
     * @param string           $value
     * @param string[]         $intents
     * @param Attribute[]|null $attributes
     * @param string|null      $tag
     */
    public function __construct(
        string $inputId,
        string $name,
        string $value = '',
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= [];
        $attributes[Html5::ATTR_ID] = new Attribute(Html5::ATTR_ID, $inputId);
        if (!in_array(Html5::ATTR_TYPE, $attributes)) {
            $attributes[Html5::ATTR_TYPE] = new Attribute(Html5::ATTR_TYPE, static::DEFAULT_TYPE);
        }
        $attributes[Html5::ATTR_NAME] = new Attribute(Html5::ATTR_NAME, $name);

        parent::__construct(null, $intents, $attributes, $tag);

        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $values = $this->attributes[Html5::ATTR_NAME]->getValues();
        if (null === $values) {
            return '';
        }

        return implode(' ', $values);
    }

    /**
     * @suppress PhanParamSignatureMismatch
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getAttribute(Html5::ATTR_VALUE)->getValue();
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

        $this->attributes[Html5::ATTR_VALUE] = new Attribute(Html5::ATTR_VALUE, htmlspecialchars($value));

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return TagHelper::toString($this->tag, '', $this->getAttributes());
    }
}
