<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\I18n\ITranslator;

class ButtonWithIcon extends Button implements ITemplater
{
    /**
     *   %1$s - text
     *   %2$s - icon
     */
    const DEFAULT_TEMPLATE = '%2$s %1$s';

    const ICON_INTENT = 'button-icon';
    const TEXT_INTENT = 'button-text';

    /** @var string */
    protected $template = self::DEFAULT_TEMPLATE;

    /** @var IComponent */
    protected $text;

    /** @var IComponent */
    protected $icon;

    /**
     * ButtonWithIcon constructor.
     *
     * @param IComponent  $text
     * @param IComponent  $icon
     * @param string[]    $intents
     * @param string[][]  $attributes
     * @param string|null $tag
     */
    public function __construct(
        IComponent $text,
        IComponent $icon,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->text = $text->addIntent(static::TEXT_INTENT);
        $this->icon = $icon->addIntent(static::ICON_INTENT);
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
     * @return string
     */
    public function __toString(): string
    {
        $content = sprintf(
            $this->template,
            (string)$this->text,
            (string)$this->icon
        );

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);

        return $content;
    }
}
