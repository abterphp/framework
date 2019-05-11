<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Component\Button;

class DefaultButtons extends Component
{
    const DEFAULT_TAG = 'div';

    const BTN_CONTENT_SAVE            = 'framework:save';
    const BTN_CONTENT_SAVE_AND_BACK   = 'framework:saveAndBack';
    const BTN_CONTENT_SAVE_AND_EDIT   = 'framework:saveAndEdit';
    const BTN_CONTENT_SAVE_AND_CREATE = 'framework:saveAndCreate';
    const BTN_CONTENT_BACK_TO_GRID    = 'framework:backToGrid';

    const BTN_NAME_NEXT = 'next';

    const BTN_VALUE_NEXT_NONE   = '';
    const BTN_VALUE_NEXT_EDIT   = 'edit';
    const BTN_VALUE_NEXT_CREATE = 'create';
    const BTN_VALUE_NEXT_BACK   = 'back';

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
     * @return $this
     */
    public function addSaveAndBack(): DefaultButtons
    {
        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = [static::BTN_VALUE_NEXT_BACK];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_SAVE_AND_BACK,
            [Button::INTENT_PRIMARY, Button::INTENT_FORM],
            $attributes
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaveAndEdit(): DefaultButtons
    {
        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = [static::BTN_VALUE_NEXT_EDIT];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_SAVE_AND_EDIT,
            [Button::INTENT_DEFAULT, Button::INTENT_FORM],
            $attributes
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaveAndCreate(): DefaultButtons
    {
        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = [static::BTN_VALUE_NEXT_CREATE];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_SAVE_AND_CREATE,
            [Button::INTENT_DEFAULT, Button::INTENT_FORM],
            $attributes
        );

        return $this;
    }

    /**
     * @param string $showUrl
     *
     * @return $this
     */
    public function addBackToGrid(string $showUrl): DefaultButtons
    {
        $attributes = [
            Html5::ATTR_HREF => [$showUrl],
        ];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_BACK_TO_GRID,
            [Button::INTENT_DANGER, Button::INTENT_FORM],
            $attributes,
            Html5::TAG_A
        );

        return $this;
    }
}
