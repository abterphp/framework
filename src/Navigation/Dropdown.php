<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;

class Dropdown extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_UL;
    protected const CONTENT_TYPE = Item::class;

    public const WRAPPER_INTENT = 'dropdown-wrapper-intent';

    protected ?ITag $wrapper = null;
    protected INode $prefix;
    protected INode $postfix;

    /** @var Item[] */
    protected array $content = [];

    /**
     * Component constructor.
     *
     * @param INode[]|INode|string|null    $content
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $this->wrapper = new Tag(null, [static::WRAPPER_INTENT], null, Html5::TAG_DIV);

        $this->prefix  = new Node();
        $this->postfix = new Node();

        parent::__construct($content, $intents, $attributes, $tag);
    }

    /**
     * @return ITag|null
     */
    public function getWrapper(): ?ITag
    {
        return $this->wrapper;
    }

    /**
     * @param ITag|null $tag
     *
     * @return $this
     */
    public function setWrapper(?ITag $tag): self
    {
        $this->wrapper = $tag;

        return $this;
    }

    /**
     * @return INode
     */
    public function getPrefix(): INode
    {
        return $this->prefix;
    }

    /**
     * @param INode $tag
     *
     * @return $this
     */
    public function setPrefix(INode $tag): self
    {
        $this->prefix = $tag;

        return $this;
    }

    /**
     * @return INode
     */
    public function getPostfix(): INode
    {
        return $this->postfix;
    }

    /**
     * @param INode $tag
     *
     * @return $this
     */
    public function setPostfix(INode $tag): self
    {
        $this->postfix = $tag;

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = [$this->prefix, $this->postfix];

        if ($this->wrapper) {
            $nodes[] = $this->wrapper;
        }

        return array_merge($nodes, $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = Tag::__toString();
        $content = $this->prefix . $content . $this->postfix;

        if ($this->wrapper) {
            $this->wrapper->setContent($content);

            return (string)$this->wrapper;
        }

        return $content;
    }
}
