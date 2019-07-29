<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\ITranslator;
use InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Collection implements ICollection
{
    const ERROR_INVALID_OFFSET       = 'Offset must be a positive integer and not larger than number of items';
    const ERROR_INVALID_TYPE_ARG     = 'Provided value must be an object instance of "%s", type "%s" is found';
    const ERROR_INVALID_INSTANCE_ARG = 'Provided value must be an instance of "%s", not an instance of "%s"';
    const ERROR_INVALID_TYPE_RETURN  = 'Retrieved value is not an instance of "%s"';

    /** @var INode[] */
    protected $nodes = [];

    /** @var string */
    protected $nodeClass = INode::class;

    /** @var ITranslator */
    protected $translator;

    /** @var int */
    protected $position = 0;

    /**
     * Intents are a way to achieve frontend-framework independence.
     * A button with an intention of "primary-action" can then
     * receive a "btn btn-primary" class from a Bootstrap-based
     * decorator
     *
     * @var string[]
     */
    protected $intents = [];

    use NodeContainerTrait;

    /**
     * Collection constructor.
     *
     * @param INode[]|INode|string|null $content
     */
    public function __construct($content = null, array $intents = [])
    {
        $this->setContent($content);
        $this->setIntent(...$intents);
    }

    /**
     * @param int|null $offset
     * @param INode    $value
     */
    public function offsetSet($offset, $value)
    {
        $this->verifyArgument($value);

        if (is_null($offset)) {
            $this->nodes[] = $value;
        } elseif (!is_int($offset) || $offset < 0 || $offset > count($this->nodes)) {
            throw new \InvalidArgumentException(static::ERROR_INVALID_OFFSET);
        } else {
            $this->nodes[$offset] = $value;
        }
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->nodes[$offset]);
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->nodes[$offset]);
    }

    /**
     * @param int $offset
     *
     * @return INode
     */
    public function offsetGet($offset)
    {
        return isset($this->nodes[$offset]) ? $this->nodes[$offset] : null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->nodes);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return INode
     */
    public function current()
    {
        return $this->nodes[$this->position];
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->nodes[$this->position]);
    }

    /**
     * @param string $intent
     *
     * @return bool
     * @see Node::$intents
     *
     */
    public function hasIntent(string $intent): bool
    {
        return in_array($intent, $this->intents, true);
    }

    /**
     * @return string[]
     * @see Node::$intents
     *
     */
    public function getIntents(): array
    {
        return $this->intents;
    }

    /**
     * @param string ...$intent
     *
     * @return INode
     * @see Node::$intents
     *
     */
    public function setIntent(string ...$intent): INode
    {
        $this->intents = $intent;

        return $this;
    }

    /**
     * @param string ...$intent
     *
     * @return $this
     */
    public function addIntent(string ...$intent): INode
    {
        $this->intents = array_merge($this->intents, $intent);

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
     * @param INode[]|INode|string|null $content
     *
     * @return $this
     */
    public function setContent($content = null): INode
    {
        if (null === $content) {
            $this->nodes = [];

            return $this;
        }

        if (is_string($content)) {
            $content = $this->createNode($content);
        }

        if (is_scalar($content)) {
            // this should throw a nicely formatted exception
            $this->verifyArgument($content);
        }

        if (is_object($content)) {
            $content = [$content];
        }

        foreach ($content as $node) {
            $this->verifyArgument($node);
        }

        $this->nodes = $content;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return INode
     */
    protected function createNode(string $content): INode
    {
        switch ($this->nodeClass) {
            case INode::class:
                return new Node($content);
            default:
                return new $this->nodeClass($content);
        }
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
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Inserts a number of nodes before a given node
     * It will also call the children to execute the same operation if the node was not found
     *
     * @param INode $nodeToFind
     * @param INode ...$nodes
     *
     * @return bool
     */
    public function insertBefore(INode $nodeToFind, INode... $nodes): bool
    {
        $key = $this->findNodeKey($nodeToFind);
        if (0 === $key) {
            $this->nodes = array_merge($nodes, $this->nodes);
        } elseif (null !== $key) {
            array_splice($this->nodes, $key, 0, $nodes);
        }

        if ($key !== null) {
            return true;
        }

        foreach ($this->nodes as $node) {
            if (!($node instanceof ICollection)) {
                continue;
            }

            if ($node->insertBefore($nodeToFind, ...$nodes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Inserts a number of nodes after a given node
     * It will also call the children to execute the same operation if the node was not found
     *
     * @param INode $nodeToFind
     * @param INode ...$nodes
     *
     * @return bool
     */
    public function insertAfter(INode $nodeToFind, INode... $nodes): bool
    {
        $key = $this->findNodeKey($nodeToFind);
        if (count($this->nodes) - 1 === $key) {
            $this->nodes = array_merge($this->nodes, $nodes);
        } elseif (null !== $key) {
            array_splice($this->nodes, $key + 1, 0, $nodes);
        }

        if ($key !== null) {
            return true;
        }

        foreach ($this->nodes as $node) {
            if (!($node instanceof ICollection)) {
                continue;
            }

            if ($node->insertAfter($nodeToFind, ...$nodes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replaces a given node with a number of nodes
     * It will also call the children to execute the same operation if the node was not found
     *
     * @param INode $nodeToFind
     * @param INode ...$nodes
     *
     * @return bool
     */
    public function replace(INode $nodeToFind, INode... $nodes): bool
    {
        $key = $this->findNodeKey($nodeToFind);
        if ($key !== null) {
            array_splice($this->nodes, $key, 1, $nodes);

            return true;
        }

        foreach ($this->nodes as $node) {
            if (!($node instanceof ICollection)) {
                continue;
            }

            if ($node->replace($nodeToFind, ...$nodes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes a given node
     * It will also call the children to execute the same operation if the node was not found
     *
     * @param INode $nodeToFind
     *
     * @return bool
     */
    public function remove(INode $nodeToFind): bool
    {
        $key = $this->findNodeKey($nodeToFind);
        if ($key !== null) {
            unset($this->nodes[$key]);
            $this->nodes = array_values($this->nodes);

            return true;
        }

        foreach ($this->nodes as $node) {
            if (!($node instanceof ICollection)) {
                continue;
            }

            if ($node->remove($nodeToFind)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param INode $nodeToFind
     *
     * @return int|null
     */
    protected function findNodeKey(INode $nodeToFind): ?int
    {
        $objectId = spl_object_id($nodeToFind);

        foreach ($this->nodes as $key => $node) {
            if (spl_object_id($node) === $objectId) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Checks if the current component matches the arguments provided
     *
     * @param string|null $className
     * @param string      ...$intents
     *
     * @return bool
     */
    public function isMatch(?string $className = null, string ...$intents): bool
    {
        if ($className && !($this instanceof $className)) {
            return false;
        }

        foreach ($intents as $intent) {
            if (!in_array($intent, $this->intents)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $list = [];
        foreach ($this->nodes as $node) {
            $list[] = (string)$node;
        }

        $content = implode("\n", $list);

        return $content;
    }

    /**
     * @param mixed $content
     *
     * @throws InvalidArgumentException
     */
    protected function verifyArgument($content)
    {
        if ($content instanceof $this->nodeClass) {
            return;
        }

        $type = gettype($content);
        if ($type !== 'object') {
            throw new InvalidArgumentException(sprintf(static::ERROR_INVALID_TYPE_ARG, $this->nodeClass, $type));
        }

        throw new InvalidArgumentException(
            sprintf(static::ERROR_INVALID_INSTANCE_ARG, $this->nodeClass, get_class($content))
        );
    }
}
