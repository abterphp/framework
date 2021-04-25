<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Contentless;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\IStringer;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\Node;
use InvalidArgumentException;
use LogicException;

class Navigation extends Contentless
{
    public const ROLE_NAVIGATION = 'navigation';

    public const INTENT_NAVBAR    = 'navbar';
    public const INTENT_FOOTER    = 'footer';
    public const INTENT_PRIMARY   = 'primary';
    public const INTENT_SECONDARY = 'secondary';

    protected const DEFAULT_TAG = Html5::TAG_UL;
    protected const CONTENT_TYPE = Item::class;
    protected const SEPARATOR = "\n";

    protected const ERROR_INVALID_TAG_FOR_ITEM_CREATION = 'item creation is not allowed for navigation type: %s';
    protected const ERROR_NAVIGATION_OFFSET_NOT_ALLOWED = 'navigation offsets are not allowed';
    protected const ERROR_UNEXPECTED_OFFSET_VALUE_COMBINATION = 'Unexpected offset-value combination: %s %s';

    protected INode $prefix;
    protected INode $postfix;

    protected ?ITag $wrapper = null;

    /** @var array<int,Item[]> */
    protected array $itemsByWeight = [];

    /** @var int highest key of $itemsByWeight */
    protected int $highestWeight = 0;

    /** @var Item[] */
    protected array $content = [];

    /**
     * Navigation constructor.
     *
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(array $intents = [], ?array $attributes = null, ?string $tag = null)
    {
        parent::__construct($intents, $attributes, $tag);

        $this->prefix  = new Node();
        $this->postfix = new Node();
    }

    /**
     * @param int  $weight
     * @param Item ...$items
     *
     * @return $this
     */
    public function addWithWeight(int $weight, Item ...$items): self
    {
        foreach ($items as $item) {
            $this->itemsByWeight[$weight][] = $item;
        }

        $this->highestWeight = $this->highestWeight > $weight ? $this->highestWeight : $weight;

        return $this;
    }

    /**
     * @param INode ...$items
     *
     * @return $this
     */
    public function add(INode ...$items): self
    {
        foreach ($items as $item) {
            assert($item instanceof Item);
            $this->itemsByWeight[$this->highestWeight][] = $item;
        }

        return $this;
    }

    protected function resort(): void
    {
        ksort($this->itemsByWeight);

        $content = [];
        foreach ($this->itemsByWeight as $items) {
            $content = array_merge($content, $items);
        }

        $this->content = $content;
    }

    /**
     * @return INode
     */
    public function getPrefix(): INode
    {
        return $this->prefix;
    }

    /**
     * @param INode $prefix
     *
     * @return $this
     */
    public function setPrefix(INode $prefix): self
    {
        $this->prefix = $prefix;

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
     * @param INode $postfix
     *
     * @return $this
     */
    public function setPostfix(INode $postfix): self
    {
        $this->postfix = $postfix;

        return $this;
    }

    /**
     * @return ITag|null
     */
    public function getWrapper(): ?ITag
    {
        return $this->wrapper;
    }

    /**
     * @param ITag|null $wrapper
     *
     * @return $this
     */
    public function setWrapper(?ITag $wrapper): self
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        $this->resort();

        return $this->content;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = array_merge([$this->prefix, $this->postfix], $this->getNodes());

        if ($this->wrapper) {
            $nodes[] = $this->wrapper;
        }

        return $nodes;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->resort();

        $content = parent::__toString();

        if ($this->wrapper) {
            $content = (string)$this->wrapper->setContent($content);
        }

        $prefix  = $this->prefix ? (string)$this->prefix : '';
        $postfix = $this->postfix ? (string)$this->postfix : '';

        return $prefix . $content . $postfix;
    }

    /**
     * @param int $offset
     *
     * @return INode|null
     */
    public function offsetGet($offset): ?INode
    {
        $this->resort();

        return $this->content[$offset] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $this->resort();

        return count($this->content);
    }

    public function rewind(): void
    {
        $this->resort();

        $this->position = 0;
    }

    /**
     * @param int|null $offset
     * @param INode    $value
     */
    public function offsetSet($offset, $value): void
    {
        assert($value instanceof Item);

        if (is_null($offset)) {
            $this->addWithWeight($this->highestWeight, $value);
            return;
        } elseif ($offset < 0 || $offset > $this->count()) {
            throw new InvalidArgumentException(static::ERROR_INVALID_OFFSET);
        }

        $count = 0;
        foreach ($this->itemsByWeight as $weight => $values) {
            $diff = $offset - $count;
            if (count($values) + $count >= $offset) {
                array_splice($this->itemsByWeight[$weight], $diff, 1, [$value]);

                break;
            } else {
                $count += count($values);
            }
        }
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset): void
    {
        $count = 0;
        foreach ($this->itemsByWeight as $weight => $values) {
            $diff = $offset - count($values);
            if (count($values) + $count >= $offset) {
                array_splice($this->itemsByWeight[$weight], $diff, 1);

                break;
            } else {
                $count += count($values);
            }
        }
    }

    /**
     * @param array<string|IStringer>|string|IStringer|null $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        if (null === $content) {
            return $this;
        }

        throw new LogicException(self::ERROR_NO_CONTENT);
    }
}
