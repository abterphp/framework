<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\INode;

class Label extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_LABEL;

    public const INTENT_TOGGLE = 'toggle';

    protected string $template;

    /** @var array<string,null|string|string[]> */
    protected array $attributes = [
        Html5::ATTR_FOR => '',
    ];

    /**
     * Label constructor.
     *
     * @param string                    $inputId
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param array                     $attributes
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
