<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\ITranslator;
use Closure;

interface INode extends IStringer
{
    /**
     * @param array<string|IStringer>|string|IStringer|null $content
     *
     * @return $this
     */
    public function setContent($content): self;

    /**
     * @return INode[]
     */
    public function getNodes(): array;

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array;

    /**
     * @return INode[]
     */
    public function forceGetNodes(): array;

    /**
     * @param string $intent
     *
     * @return bool
     */
    public function hasIntent(string $intent): bool;

    /**
     * @return string[]
     */
    public function getIntents(): array;

    /**
     * @param string ...$intent
     *
     * @return $this
     */
    public function setIntent(string ...$intent): self;

    /**
     * Adds a single intent
     *
     * @param string ...$intent
     *
     * @return $this
     */
    public function addIntent(string ...$intent): self;

    /**
     * @param ITranslator|null $translator
     *
     * @return $this
     */
    public function setTranslator(?ITranslator $translator): self;

    /**
     * @return ITranslator|null
     */
    public function getTranslator(): ?ITranslator;

    /**
     * Checks if the current component matches the arguments provided
     *
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return bool
     */
    public function isMatch(?string $className = null, ?Closure $matcher = null, string ...$intents): bool;

    /**
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return INode|null
     */
    public function find(?string $className = null, ?Closure $matcher = null, string ...$intents): ?INode;

    /**
     * @param string|null  $className
     * @param Closure|null $matcher
     * @param string       ...$intents
     *
     * @return INode[]
     */
    public function findAll(?string $className = null, ?Closure $matcher = null, string ...$intents): array;

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
    ): array;

    /**
     * Replaces a given node with a number of nodes
     * It will also call the children to execute the same operation if the node was not found
     *
     * @param INode $itemToFind
     * @param INode ...$items
     *
     * @return bool
     */
    public function replace(INode $itemToFind, INode ...$items): bool;

    /**
     * Add items
     *
     * @param INode ...$items
     *
     * @return $this
     */
    public function add(INode ...$items): self;
}
