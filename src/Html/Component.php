<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\StringHelper;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Component extends Collection implements IComponent
{
    use TagTrait;

    protected const ERROR_INVALID_INTENT    = 'intent must be a string';
    protected const ERROR_INVALID_ATTRIBUTE = 'invalid attribute';

    protected const DEFAULT_TAG = Html5::TAG_SPAN;

    public const INTENT_HIDDEN = 'hidden';
    public const INTENT_SMALL  = 'small';
    public const INTENT_LARGE  = 'large';
    public const INTENT_ICON   = 'icon';

    public const CLASS_HIDDEN = 'hidden';

    /**
     * Component constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param array                     $attributes
     * @param string|null               $tag
     */
    public function __construct($content = null, array $intents = [], array $attributes = [], ?string $tag = null)
    {
        parent::__construct($content, $intents);

        $this->appendToAttributes($attributes);
        $this->setTag($tag);
    }

    /**
     * @param INode $node
     *
     * @return int|null
     */
    public function find(INode $node): ?int
    {
        return $this->findNodeKey($node);
    }

    /**
     * Tries to find the first child that matches the arguments provided
     *
     * @param string|null $className
     * @param string      ...$intents
     *
     * @return IComponent|null
     */
    public function findFirstChild(?string $className = null, string ...$intents): ?IComponent
    {
        foreach ($this->nodes as $node) {
            if (!($node instanceof IComponent)) {
                continue;
            }

            if (!$node->isMatch($className, ...$intents)) {
                continue;
            }

            return $node;
        }

        return null;
    }

    /**
     * Collects all children, grandchildren, etc that match the arguments provided
     *
     * @param string|null $className
     * @param array       $intents
     * @param int         $depth maximum level of recursion, -1 or smaller means infinite, 0 is direct children only
     *
     * @return IComponent[]
     */
    public function collect(?string $className = null, array $intents = [], int $depth = -1): array
    {
        $matches = [];

        foreach ($this->getDescendantNodes($depth) as $node) {
            if ($node->isMatch($className, ...$intents)) {
                $matches[] = $node;
            }
        }

        return $matches;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = parent::__toString();

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);

        return $content;
    }
}
