<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Html\Helper\Collection;
use AbterPhp\Framework\I18n\ITranslator;
use ArrayAccess;
use Closure;
use Countable;
use Iterator;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Node implements INode, Iterator, ArrayAccess, Countable
{
    protected const ERROR_INVALID_OFFSET = 'Offset must be a positive integer and not larger than number of items'; // phpcs:ignore

    protected const CONTENT_TYPE = '';

    protected const SEPARATOR = '';

    /** @var array<string|INode> */
    protected array $content = [];

    /** @var int */
    protected int $position = 0;

    /**
     * Intents are a way to achieve frontend-framework independence.
     * A button with an intention of "primary-action" can then
     * receive a "btn btn-primary" class from a Bootstrap-based
     * decorator
     *
     * @var string[]
     */
    protected array $intents = [];

    protected ?ITranslator $translator = null;

    /**
     * Node constructor.
     *
     * @param array<string|INode>|string|INode|null $content
     * @param string                                ...$intents
     */
    public function __construct($content = null, string ...$intents)
    {
        if ($content !== null) {
            $this->setContent($content);
        }

        $this->setIntent(...$intents);
    }

    /**
     * @param array<string|IStringer>|string|IStringer|null $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        $this->check($content);

        $this->content = [];

        if (null === $content) {
            return $this;
        }

        if (!is_array($content)) {
            $content = [$content];
        }

        foreach ($content as $item) {
            if ($item instanceof INode) {
                $this->content[] = $item;
            } else {
                $this->content[] = (string)$item;
            }
        }

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        $result = [];

        foreach ($this->content as $item) {
            if ($item instanceof INode) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return $this->getNodes();
    }

    /**
     * @return INode[]
     */
    public function forceGetNodes(): array
    {
        $result = [];

        foreach ($this->content as $item) {
            if ($item instanceof INode) {
                $result[] = $item;
            } else {
                $result[] = new Node($item);
            }
        }

        return $result;
    }

    /**
     * @param string $intent
     *
     * @return bool
     */
    public function hasIntent(string $intent): bool
    {
        return in_array($intent, $this->intents, true);
    }

    /**
     * @return string[]
     */
    public function getIntents(): array
    {
        return $this->intents;
    }

    /**
     * @param string ...$intent
     *
     * @return $this
     */
    public function setIntent(string ...$intent): self
    {
        $this->intents = $intent;

        return $this;
    }

    /**
     * @param string ...$intent
     *
     * @return $this
     */
    public function addIntent(string ...$intent): self
    {
        $this->intents = array_merge($this->intents, $intent);

        return $this;
    }

    /**
     * @param ITranslator|null $translator
     *
     * @return $this
     */
    public function setTranslator(?ITranslator $translator): self
    {
        $this->translator = $translator;

        foreach ($this->getExtendedNodes() as $node) {
            $node->setTranslator($translator);
        }

        return $this;
    }

    /**
     * @return ITranslator|null
     */
    public function getTranslator(): ?ITranslator
    {
        return $this->translator;
    }

    /**
     * Checks if the current component matches the arguments provided
     *
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return bool
     */
    public function isMatch(?string $className = null, ?Closure $matcher = null, string ...$intents): bool
    {
        if ($className && !($this instanceof $className)) {
            return false;
        }

        if ($matcher && !$matcher($this)) {
            return false;
        }

        foreach ($intents as $intent) {
            if (!in_array($intent, $this->intents, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return INode|null
     */
    public function find(?string $className = null, ?Closure $matcher = null, string ...$intents): ?INode
    {
        if ($this->isMatch($className, $matcher, ...$intents)) {
            return $this;
        }

        foreach ($this->getNodes() as $node) {
            $found = $node->find($className, $matcher, ...$intents);
            if ($found) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Finds all sub-nodes which match a certain criteria
     *
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return INode[]
     */
    public function findAll(?string $className = null, ?Closure $matcher = null, string ...$intents): array
    {
        return $this->findAllShallow(-1, $className, $matcher, ...$intents);
    }

    /**
     * @param int          $maxDepth
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return INode[]
     */
    public function findAllShallow(
        int $maxDepth,
        ?string $className = null,
        ?Closure $matcher = null,
        string ...$intents
    ): array {
        $result = [];

        if ($this->isMatch($className, $matcher, ...$intents)) {
            $result[] = $this;
        }

        if ($maxDepth === 0) {
            return $result;
        }

        foreach ($this->getNodes() as $node) {
            $result = array_merge($result, $node->findAllShallow($maxDepth - 1, $className, $matcher, ...$intents));
        }

        return $result;
    }

    /**
     * @param INode|string $itemToFind
     *
     * @return int|null
     */
    public function findKey($itemToFind): ?int
    {
        foreach ($this->content as $key => $item) {
            if ($item === $itemToFind) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Replaces a given node with a number of nodes
     * It will also call the children to execute the same operation if the node was not found
     *
     * @param INode $itemToFind
     * @param INode ...$items
     *
     * @return bool
     */
    public function replace(INode $itemToFind, INode ...$items): bool
    {
        $this->check($items);

        $key = $this->findKey($itemToFind);
        if ($key !== null) {
            array_splice($this->content, $key, 1, $items);

            return true;
        }

        foreach ($this->content as $item) {
            if ($item instanceof INode && $item->replace($itemToFind, ...$items)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param INode ...$items
     *
     * @return $this
     */
    public function add(INode ...$items): self
    {
        $this->check($items);

        $this->content = array_merge($this->content, $items);

        return $this;
    }

    /**
     * @param array<string|INode>|string|INode|null $content
     */
    protected function check($content = null)
    {
        if ($content === null) {
            return;
        }

        if (!is_array($content)) {
            $content = [$content];
        }

        if (!static::CONTENT_TYPE) {
            assert(Collection::allNodes($content));
        } else {
            assert(Collection::allInstanceOf($content, static::CONTENT_TYPE));
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $items = [];
        foreach ($this->content as $item) {
            if (is_scalar($item) && $this->getTranslator()) {
                $i = $this->getTranslator()->translate($item);
            } else {
                $i = (string)$item;
            }
            if ($i !== '') {
                $items[] = $i;
            }
        }

        return join(static::SEPARATOR, $items);
    }

    /**
     * @param int|null $offset
     * @param INode    $value
     */
    public function offsetSet($offset, $value): void
    {
        assert(Collection::allInstanceOf([$value], static::CONTENT_TYPE));

        if (is_null($offset)) {
            $this->content[] = $value;
        } elseif ($offset < 0 || $offset > count($this->content)) {
            throw new \InvalidArgumentException(static::ERROR_INVALID_OFFSET);
        } else {
            $this->content[$offset] = $value;
        }
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->content[$offset]);
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->content[$offset]);
    }

    /**
     * @param int $offset
     *
     * @return INode|null
     */
    public function offsetGet($offset): ?INode
    {
        return $this->content[$offset] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->content);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @return INode
     */
    public function current(): INode
    {
        return $this->content[$this->position];
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->content[$this->position]);
    }
}
