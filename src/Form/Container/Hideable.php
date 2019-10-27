<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Component\Button;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;

class Hideable extends Component implements ITemplater
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;

    protected const CLASS_HIDABLE = 'hidable';
    protected const CLASS_HIDER   = 'hider';

    /**
     * %1$s - hider button
     * %2$s - nodes
     */
    protected const DEFAULT_TEMPLATE = '
        <p class="hider">
            %1$s
        </p>
        <div class="hidee">
            %2$s
        </div>';

    /** @var string */
    protected $template = self::DEFAULT_TEMPLATE;

    /** @var array */
    protected $attributes = [
        Html5::ATTR_CLASS => [self::CLASS_HIDABLE],
    ];

    /** @var Button */
    protected $hiderBtn;

    /**
     * Hideable constructor.
     *
     * @param string $hiderBtnLabel
     * @param array $intent
     * @param array $attributes
     * @param string|null $tag
     */
    public function __construct(
        string $hiderBtnLabel,
        array $intent = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct(null, $intent, $attributes, $tag);

        $this->hiderBtn = new Button(
            $hiderBtnLabel,
            [Button::INTENT_INFO],
            [Html5::ATTR_TYPE => [Button::TYPE_BUTTON]]
        );
    }

    /**
     * @param string $template
     *
     * @return INode
     */
    public function setTemplate(string $template): INode
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Button
     */
    public function getHiderBtn(): Button
    {
        return $this->hiderBtn;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return array_merge([$this->hiderBtn], $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = sprintf(
            $this->template,
            (string)$this->hiderBtn,
            Collection::__toString()
        );

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);

        return $content;
    }
}
