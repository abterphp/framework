<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Component\Button;

class DefaultButtons extends Component
{
    const DEFAULT_TAG = 'div';

    const BTN_CONTENT_SAVE          = 'framework:save';
    const BTN_CONTENT_SAVE_AND_EDIT = 'framework:saveAndEdit';
    const BTN_CONTENT_BACK_TO_GRID  = 'framework:backToGrid';

    const BTN_NAME_CONTINUE = 'continue';

    /** @var Button[] */
    protected $components;

    /** @var array */
    protected $attributes = [];

    /** @var array */
    protected $btnAttributes = [
        Html5::ATTR_NAME  => [self::BTN_NAME_CONTINUE],
        Html5::ATTR_TYPE  => [Button::TYPE_SUBMIT],
        Html5::ATTR_VALUE => ['0'],
    ];

    /**
     * DefaultButtons constructor.
     *
     * @param string      $showUrl
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(
        string $showUrl,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->addSave();
        $this->addSaveAndEdit();
        $this->addBackToGrid($showUrl);
    }

    protected function addSave()
    {
        $this->nodes[] = new Button(
            static::BTN_CONTENT_SAVE,
            [Button::INTENT_PRIMARY, Button::INTENT_FORM],
            $this->btnAttributes
        );
    }

    protected function addSaveAndEdit()
    {
        $attributes = $this->btnAttributes;

        $attributes[Html5::ATTR_VALUE] = ['1'];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_SAVE_AND_EDIT,
            [Button::INTENT_SUCCESS, Button::INTENT_FORM],
            $attributes
        );
    }

    /**
     * @param string $showUrl
     */
    protected function addBackToGrid(string $showUrl)
    {
        $attributes = [
            Html5::ATTR_HREF => [$showUrl],
        ];

        $this->nodes[] = new Button(
            static::BTN_CONTENT_BACK_TO_GRID,
            [Button::INTENT_PRIMARY, Button::INTENT_FORM],
            $attributes,
            Html5::TAG_A
        );
    }
}
