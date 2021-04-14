<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Element\IElement;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\Html\NodeContainerTrait;
use AbterPhp\Framework\Html\Tag;

class FormGroup extends Tag implements ITemplater
{
    use NodeContainerTrait;

    protected const DEFAULT_TAG = Html5::TAG_DIV;

    /**
     * %1$s - label
     * %2$s - input
     * %3$s - help
     */
    protected const DEFAULT_TEMPLATE = '%1$s%2$s%3$s';

    public const CLASS_COUNTABLE = 'countable';

    public const CLASS_REQUIRED = 'required';

    protected IElement $input;

    protected Label $label;

    protected ?INode $help;

    protected string $template = self::DEFAULT_TEMPLATE;

    /**
     * FormGroup constructor.
     *
     * @param IElement        $input
     * @param Label           $label
     * @param INode|null      $help
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     */
    public function __construct(
        IElement $input,
        Label $label,
        ?INode $help = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->label = $label;
        $this->input = $input;
        $this->help  = $help;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue(string $value): FormGroup
    {
        $this->input->setValue($value);

        return $this;
    }

    /**
     * @return IElement|null
     */
    public function getInput(): ?IElement
    {
        return $this->input;
    }

    /**
     * @return Label|null
     */
    public function getLabel(): ?Label
    {
        return $this->label;
    }

    /**
     * @return INode|null
     */
    public function getHelp(): ?INode
    {
        return $this->help;
    }

    /**
     * @return IElement[]
     */
    public function getElements(): array
    {
        return [$this->input];
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate(string $template): INode
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = [$this->label, $this->input];

        if ($this->help) {
            $nodes[] = $this->help;
        }

        return array_merge($nodes, $this->getNodes());
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $help = $this->help ?: '';

        $content = sprintf(
            $this->template,
            (string)$this->label,
            (string)$this->input,
            (string)$help
        );

        return TagHelper::toString($this->tag, $content, $this->attributes);
    }
}
