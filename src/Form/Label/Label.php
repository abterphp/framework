<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\INode;

class Label extends Component
{
    const DEFAULT_TAG = Html5::TAG_LABEL;

    const INTENT_TOGGLE = 'toggle';

    /** @var string */
    protected $template;

    /** @var array */
    protected $attributes = [
        Html5::ATTR_FOR => '',
    ];

    /**
     * Label constructor.
     *
     * @param string                    $inputId
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param string[][]                $attributes
     * @param string|null               $tag
     */
    public function __construct(
        string $inputId,
        $content = null,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        $this->attributes[Html5::ATTR_FOR] = [$inputId];

        parent::__construct($content, $intents, $attributes, $tag);
    }
}
