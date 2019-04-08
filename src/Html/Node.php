<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\ITranslator;

class Node implements INode
{
    /** @var string|INode */
    protected $content;

    /**
     * Intents are a way to achieve frontend-framework independency.
     * A button with an intention of "primary-action" can then
     * receive a "btn btn-primary" class from a Bootstrap-based
     * decorator
     *
     * @var string[]
     */
    protected $intents = [];

    /** @var ITranslator */
    protected $translator;

    /**
     * Node constructor.
     *
     * @param mixed $content
     * @param array $intents
     */
    public function __construct($content = null, array $intents = [])
    {
        $this->setContent($content);
        $this->setIntent(...$intents);
    }

    /**
     * @return string
     */
    public function getRawContent(): string
    {
        if ($this->content instanceof Node) {
            return $this->content->getRawContent();
        }

        return $this->content;
    }

    /**
     * @param string|INode
     *
     * @return $this
     */
    public function setContent($content): INode
    {
        if (null === $content) {
            $this->content = '';

            return $this;
        }

        if (is_scalar($content)) {
            $content = (string)$content;
        }

        if (!is_string($content) && !($content instanceof INode)) {
            throw new \InvalidArgumentException();
        }

        $this->content = $content;

        return $this;
    }

    /**
     * @see Node::$intents
     *
     * @param string $intent
     *
     * @return bool
     */
    public function hasIntent(string $intent): bool
    {
        return in_array($intent, $this->intents, true);
    }

    /**
     * @see Node::$intents
     *
     * @return string[]
     */
    public function getIntents(): array
    {
        return $this->intents;
    }

    /**
     * @see Node::$intents
     *
     * @param string ...$intent
     *
     * @return INode
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
     * @param ITranslator|null $translator
     *
     * @return $this
     */
    public function setTranslator(?ITranslator $translator): INode
    {
        $this->translator = $translator;

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
     * @return string
     */
    public function __toString(): string
    {
        return $this->translate($this->content);
    }

    /**
     * @param mixed $content
     *
     * @return string
     */
    public function translate($content): string
    {
        if (is_string($content) && $this->translator && $this->translator->canTranslate($content)) {
            return $this->translator->translate($content);
        }

        return (string)$content;
    }
}
