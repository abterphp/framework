<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;

class Countable extends Label implements ITemplater
{
    public const DEFAULT_SIZE = 160;

    /**
     * %1$s - nodes
     * %2$s - counter
     */
    protected const DEFAULT_TEMPLATE = '%1$s %2$s';

    protected const ATTR_DATA_COUNT = Html5::ATTR_DATA_DASH . 'count';

    protected const CLASS_COUNT = 'count';

    protected string $template = self::DEFAULT_TEMPLATE;

    protected Component $counter;

    /**
     * Countable constructor.
     *
     * @param string                    $inputId
     * @param INode[]|INode|string|null $content
     * @param int                       $size
     * @param string[]                  $intents
     * @param Attributes|null           $attributes
     * @param string|null               $tag
     */
    public function __construct(
        string $inputId,
        $content = null,
        int $size = 160,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct($inputId, $content, $intents, $attributes, $tag);

        $counterAttributes = new Attributes(
            [
                static::ATTR_DATA_COUNT => [$size],
                Html5::ATTR_CLASS       => [static::CLASS_COUNT],
            ]
        );
        $this->counter     = new Component(null, [], $counterAttributes);
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
        return array_merge([$this->counter], $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $nodes = Collection::__toString();

        $content = sprintf(
            $this->template,
            $nodes,
            (string)$this->counter
        );

        return TagHelper::toString($this->tag, $content, $this->attributes);
    }
}
