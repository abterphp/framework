<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\I18n\ITranslator;

class Countable extends Label implements ITemplater
{
    const DEFAULT_SIZE = 160;

    /**
     * %1$s - nodes
     * %2$s - counter
     */
    const DEFAULT_TEMPLATE = '%1$s %2$s';

    const ATTR_DATA_COUNT = Html5::ATTR_DATA_DASH . 'count';

    const CLASS_COUNT = 'count';

    /** @var string */
    protected $template = self::DEFAULT_TEMPLATE;

    /** @var Component */
    protected $counter;

    /**
     * Countable constructor.
     *
     * @param string                    $inputId
     * @param INode[]|INode|string|null $content
     * @param int                       $size
     * @param string[]                  $intents
     * @param string[][]                $attributes
     * @param string|null               $tag
     */
    public function __construct(
        string $inputId,
        $content = null,
        int $size = 160,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct($inputId, $content, $intents, $attributes, $tag);

        $attributes = [
            static::ATTR_DATA_COUNT => $size,
            Html5::ATTR_CLASS       => static::CLASS_COUNT,
        ];
        $this->counter = new Component(null, [], $attributes);
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

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);

        return $content;
    }
}
