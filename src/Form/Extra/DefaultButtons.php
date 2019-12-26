<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Component\Button;

class DefaultButtons extends Component
{
    protected const DEFAULT_TAG = 'div';

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

    /** @var Button[] */
    protected $components;

    /** @var array */
    protected $attributes = [];

    /** @var array */
    protected $btnAttributes = [
        Html5::ATTR_NAME  => [self::BTN_NAME_NEXT],
        Html5::ATTR_TYPE  => [Button::TYPE_SUBMIT],
        Html5::ATTR_VALUE => [self::BTN_VALUE_NEXT_NONE],
    ];

    /**
     * @return $this
     */
    public function addSave(): DefaultButtons
    {
        $this->nodes[] = new Button(
            static::BTN_CONTENT_SAVE,
            [Button::INTENT_PRIMARY, Button::INTENT_FORM],
            $this->btnAttributes
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

        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = [static::BTN_VALUE_NEXT_BACK];

        $this->nodes[] = new Button(
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

        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = [static::BTN_VALUE_NEXT_EDIT];

        $this->nodes[] = new Button(
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

        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = [static::BTN_VALUE_NEXT_CREATE];

        $this->nodes[] = new Button(
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

        $attributes = [
            Html5::ATTR_HREF => [$showUrl],
        ];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_BACK_TO_GRID,
            $intents,
            $attributes,
            Html5::TAG_A
        );

        return $this;
    }
}
