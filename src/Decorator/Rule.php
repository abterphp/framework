<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Decorator;

class Rule
{
    /** @var string[] */
    protected $requiredIntents = [];

    /** @var string|null */
    protected $requiredClassName = null;

    /** @var string[] */
    protected $defaultClasses = [];

    /** @var string[] */
    protected $intentClassMap = [];

    /** @var callable|null */
    protected $callback;

    /**
     * Rule constructor.
     *
     * @param string[]      $requiredIntents
     * @param string|null   $requiredClassName
     * @param string[]      $defaultClasses
     * @param string[]      $intentClassMap
     * @param callback|null $callback
     */
    public function __construct(
        array $requiredIntents,
        ?string $requiredClassName,
        array $defaultClasses,
        array $intentClassMap = [],
        ?callable $callback = null
    ) {
        $this->requiredIntents   = $requiredIntents;
        $this->requiredClassName = $requiredClassName;
        $this->defaultClasses    = $defaultClasses;
        $this->intentClassMap    = $intentClassMap;
        $this->callback          = $callback;
    }

    /**
     * @return string[]
     */
    public function getRequiredIntents(): array
    {
        return $this->requiredIntents;
    }

    /**
     * @return string|null
     */
    public function getRequiredClassName(): ?string
    {
        return $this->requiredClassName;
    }

    /**
     * @return string[]
     */
    public function getDefaultClasses(): array
    {
        return $this->defaultClasses;
    }

    /**
     * @return string[]
     */
    public function getIntentClassMap(): array
    {
        return $this->intentClassMap;
    }

    /**
     * @return callable|null
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }
}
