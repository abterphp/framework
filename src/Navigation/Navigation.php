<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\ICollection;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\INodeContainer;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\I18n\ITranslator;

class Navigation extends Tag implements INodeContainer
{
    protected const DEFAULT_TAG = Html5::TAG_UL;

    protected const ERROR_INVALID_TAG_FOR_ITEM_CREATION = 'item creation is not allowed for navigation type: %s';

    public const ROLE_NAVIGATION = 'navigation';

    public const INTENT_NAVBAR    = 'navbar';
    public const INTENT_FOOTER    = 'footer';
    public const INTENT_PRIMARY   = 'primary';
    public const INTENT_SECONDARY = 'secondary';

    /** @var ICollection */
    protected $prefix;

    /** @var ICollection */
    protected $postfix;

    /** @var IComponent|null */
    protected $wrapper;

    /** @var Item[][] */
    protected $itemsByWeight = [];

    /** @var Item[] */
    protected $nodes = [];

    /**
     * Navigation constructor.
     *
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(array $intents = [], array $attributes = [], ?string $tag = null)
    {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->prefix  = new Collection();
        $this->postfix = new Collection();
    }

    /**
     * @param Item $component
     * @param int  $weight
     *
     * @return $this
     */
    public function addItem(Item $component, int $weight = PHP_INT_MAX): Navigation
    {
        $this->itemsByWeight[$weight][] = $component;

        return $this;
    }

    protected function resort()
    {
        ksort($this->itemsByWeight);

        $nodes = [];
        foreach ($this->itemsByWeight as $nodesByWeight) {
            $nodes = array_merge($nodes, $nodesByWeight);
        }

        $this->nodes = $nodes;
    }

    /**
     * @return ICollection
     */
    public function getPrefix(): ICollection
    {
        return $this->prefix;
    }

    /**
     * @param ICollection $prefix
     *
     * @return $this
     */
    public function setPrefix(ICollection $prefix): Navigation
    {
        $this->prefix = $prefix;

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
     * @param ICollection $postfix
     *
     * @return $this
     */
    public function setPostfix(ICollection $postfix): Navigation
    {
        $this->postfix = $postfix;

        return $this;
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
    public function setWrapper(?IComponent $wrapper): Navigation
    {
        $this->wrapper = $wrapper;

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
     * @return INode[]
     */
    public function getNodes(): array
    {
        $this->resort();

        return $this->nodes;
    }

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getDescendantNodes(int $depth = -1): array
    {
        $nodes = [];
        foreach ($this->getNodes() as $node) {
            $nodes[] = $node;

            if ($depth !== 0 && $node instanceof INodeContainer) {
                $nodes = array_merge($nodes, $node->getDescendantNodes($depth - 1));
            }
        }

        return $nodes;
    }

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getExtendedDescendantNodes(int $depth = -1): array
    {
        $nodes = [];
        foreach ($this->getExtendedNodes() as $node) {
            $nodes[] = $node;

            if ($depth !== 0 && $node instanceof INodeContainer) {
                $nodes = array_merge($nodes, $node->getExtendedDescendantNodes($depth - 1));
            }
        }

        return $nodes;
    }

    /**
     * @param ITranslator|null $translator
     *
     * @return $this
     */
    public function setTranslator(?ITranslator $translator): INode
    {
        $this->translator = $translator;

        $nodes = $this->getExtendedNodes();
        /** @var INode $node */
        foreach ($nodes as $node) {
            $node->setTranslator($translator);
        }

        return $this;
    }

    /**
     * @param string|INode $content
     *
     * @return $this
     * @deprecated setContent is not supported on Navigation
     */
    public function setContent($content): INode
    {
        if ($content !== null) {
            throw new \LogicException('Navigation::setContent must not be called');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->resort();

        $itemContentList = [];
        foreach ($this->nodes as $node) {
            $itemContentList[] = (string)$node;
        }
        $content = implode("\n", $itemContentList);

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);
        if ($this->wrapper) {
            $content = (string)$this->wrapper->setContent($content);
        }

        $prefix  = $this->prefix ? (string)$this->prefix : '';
        $postfix = $this->postfix ? (string)$this->postfix : '';

        return $prefix . $content . $postfix;
    }
}
