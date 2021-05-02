<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component\Button;
use AbterPhp\Framework\Html\Contentless;
use AbterPhp\Framework\Html\Helper\Attributes;

class DefaultButtons extends Contentless
{
    public const BTN_CONTENT_SAVE            = 'framework:save';
    public const BTN_CONTENT_SAVE_AND_BACK   = 'framework:saveAndBack';
    public const BTN_CONTENT_SAVE_AND_EDIT   = 'framework:saveAndEdit';
    public const BTN_CONTENT_SAVE_AND_CREATE = 'framework:saveAndCreate';
    public const BTN_CONTENT_BACK_TO_GRID    = 'framework:backToGrid';

    public const BTN_NAME_NEXT = 'next';

    public const BTN_VALUE_NEXT_NONE   = '';
    public const BTN_VALUE_NEXT_EDIT   = 'edit';
    public const BTN_VALUE_NEXT_CREATE = 'create';
    public const BTN_VALUE_NEXT_BACK   = 'back';

    protected const SEPARATOR = "\n";

    protected const BTN_ATTRIBUTES = [
        Html5::ATTR_NAME  => [self::BTN_NAME_NEXT],
        Html5::ATTR_TYPE  => [Button::TYPE_SUBMIT],
        Html5::ATTR_VALUE => [self::BTN_VALUE_NEXT_NONE],
    ];

    protected const DEFAULT_TAG = 'div';

    /** @var Button[] */
    protected array $content = [];

    /**
     * @return $this
     */
    public function addSave(): DefaultButtons
    {
        $this->content[] = new Button(
            static::BTN_CONTENT_SAVE,
            [Button::INTENT_PRIMARY, Button::INTENT_FORM],
            Attributes::fromArray(static::BTN_ATTRIBUTES)
        );

        return $this;
    }

    /**
     * @param string ...$intents
     *
     * @return $this
     */
    public function addSaveAndBack(string ...$intents): DefaultButtons
    {
        $intents = $intents ?: [Button::INTENT_PRIMARY, Button::INTENT_FORM];

        $attributes                    = static::BTN_ATTRIBUTES;
        $attributes[Html5::ATTR_VALUE] = static::BTN_VALUE_NEXT_BACK;
        $attributes                    = Attributes::fromArray($attributes);

        $this->content[] = new Button(
            static::BTN_CONTENT_SAVE_AND_BACK,
            $intents,
            $attributes
        );

        return $this;
    }

    /**
     * @param string ...$intents
     *
     * @return $this
     */
    public function addSaveAndEdit(string ...$intents): DefaultButtons
    {
        $intents = $intents ?: [Button::INTENT_DEFAULT, Button::INTENT_FORM];

        $attributes                    = static::BTN_ATTRIBUTES;
        $attributes[Html5::ATTR_VALUE] = static::BTN_VALUE_NEXT_EDIT;
        $attributes                    = Attributes::fromArray($attributes);

        $this->content[] = new Button(
            static::BTN_CONTENT_SAVE_AND_EDIT,
            $intents,
            $attributes
        );

        return $this;
    }

    /**
     * @param string ...$intents
     *
     * @return DefaultButtons
     */
    public function addSaveAndCreate(string ...$intents): DefaultButtons
    {
        $intents = $intents ?: [Button::INTENT_DEFAULT, Button::INTENT_FORM];

        $attributes                    = static::BTN_ATTRIBUTES;
        $attributes[Html5::ATTR_VALUE] = static::BTN_VALUE_NEXT_CREATE;
        $attributes                    = Attributes::fromArray($attributes);

        $this->content[] = new Button(
            static::BTN_CONTENT_SAVE_AND_CREATE,
            $intents,
            $attributes
        );

        return $this;
    }

    /**
     * @param string $showUrl
     * @param string ...$intents
     *
     * @return $this
     */
    public function addBackToGrid(string $showUrl, string ...$intents): DefaultButtons
    {
        $intents = $intents ?: [Button::INTENT_DANGER, Button::INTENT_FORM];

        $attributes = Attributes::fromArray([Html5::ATTR_HREF => [$showUrl]]);

        $this->content[] = new Button(
            static::BTN_CONTENT_BACK_TO_GRID,
            $intents,
            $attributes,
            Html5::TAG_A
        );

        return $this;
    }
}
