<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Tag;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\ITemplater;

class ButtonWithIcon extends Button implements ITemplater
{
    public const INTENT_BUTTON_ICON = 'button-icon';
    public const INTENT_BUTTON_TEXT = 'button-text';

    /**
     *   %1$s - text
     *   %2$s - icon
     */
    protected const DEFAULT_TEMPLATE = '%2$s %1$s';

    protected string $template = self::DEFAULT_TEMPLATE;

    protected ITag $text;

    protected ITag $icon;

    /**
     * ButtonWithIcon constructor.
     *
     * @param ITag                         $text
     * @param ITag                         $icon
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(
        ITag $text,
        ITag $icon,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->text = $text->addIntent(static::INTENT_BUTTON_TEXT);
        $this->icon = $icon->addIntent(static::INTENT_BUTTON_ICON);
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
        return array_merge([$this->text, $this->icon], $this->getNodes());
    }

    /**
     * @return ITag
     */
    public function getText(): ITag
    {
        return $this->text;
    }

    /**
     * @return ITag
     */
    public function getIcon(): ITag
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = sprintf(
            $this->template,
            (string)$this->text,
            (string)$this->icon
        );

        return Tag::toString($this->tag, $content, $this->attributes);
    }
}
