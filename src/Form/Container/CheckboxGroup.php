<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;

class CheckboxGroup extends FormGroup
{
    protected const SPAN_INTENT = 'checkbox-span';

    /** @var IComponent */
    protected $checkboxSpan;

    /**
     * ToggleGroup constructor.
     *
     * @param Input           $input
     * @param Label           $label
     * @param INode|null      $help
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     */
    public function __construct(
        Input $input,
        Label $label,
        ?INode $help = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        $attributes ??= new Attributes();

        $input->setAttribute(new Attribute(Html5::ATTR_TYPE, Input::TYPE_CHECKBOX));

        parent::__construct($input, $label, $help, $intents, $attributes, $tag);

        $this->checkboxSpan = new Component(null, [static::SPAN_INTENT], null, Html5::TAG_SPAN);
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = [$this->label, $this->input, $this->checkboxSpan];
        if ($this->help) {
            $nodes[] = $this->help;
        }

        return array_merge($nodes, $this->getNodes());
    }

    /**
     * @return IComponent
     */
    public function getCheckboxSpan(): IComponent
    {
        return $this->checkboxSpan;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $help = $this->help ?: '';
        $this->checkboxSpan->setContent((string)$this->label);

        $this->label->setContent([$this->input, $this->checkboxSpan, $help]);

        return TagHelper::toString($this->tag, (string)$this->label, $this->attributes);
    }
}
