<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\ICollection;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;

class Dropdown extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_UL;

    public const WRAPPER_INTENT = 'dropdown-wrapper-intent';

    protected ?IComponent $wrapper = null;
    protected ICollection $prefix;
    protected ICollection $postfix;

    /** @var Item[] */
    protected array $nodes = [];

    protected string $nodeClass = Item::class;

    /**
     * Component constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param Attributes|null           $attributes
     * @param string|null               $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        $this->wrapper = new Component(null, [static::WRAPPER_INTENT], null, Html5::TAG_DIV);

        $this->prefix = new Collection();
        $this->postfix = new Collection();

        parent::__construct($content, $intents, $attributes, $tag);
    }

    /**
     * @return IComponent|null
     */
    public function getWrapper(): ?IComponent
    {
        return $this->wrapper;
    }

    /**
     * @param IComponent|null $wrapper
     *
     * @return $this
     */
    public function setWrapper(?IComponent $wrapper): Dropdown
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return ICollection
     */
    public function getPrefix(): ICollection
    {
        return $this->prefix;
    }

    /**
     * @param ICollection $collection
     *
     * @return Dropdown
     */
    public function setPrefix(ICollection $collection): Dropdown
    {
        $this->prefix = $collection;

        return $this;
    }

    /**
     * @return ICollection
     */
    public function getPostfix(): ICollection
    {
        return $this->postfix;
    }

    /**
     * @param ICollection $collection
     *
     * @return Dropdown
     */
    public function setPostfix(ICollection $collection): Dropdown
    {
        $this->postfix = $collection;

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
        $content = TagHelper::toString($this->tag, Collection::__toString(), $this->attributes);
        $content = (string)$this->prefix . $content . (string)$this->postfix;

        if ($this->wrapper) {
            $this->wrapper->setContent($content);

            return (string)$this->wrapper;
        }

        return $content;
    }
}
