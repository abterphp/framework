<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Decorator;

class Rule
{
    /** @var string[] */
    protected array $requiredIntents = [];

    /** @var string|null */
    protected ?string $requiredClassName = null;

    /** @var string[] */
    protected array $defaultClasses = [];

    /** @var string[][] */
    protected array $intentClassMap = [];

    /** @var callable */
    protected $callback;

    /**
     * Rule constructor.
     *
     * @param string[]      $requiredIntents
     * @param string|null   $requiredClassName
     * @param string[]      $defaultClasses
     * @param string[][]    $intentClassMap
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
        if (!$callback) {
            $callback = fn ($x) => $x;
        }
        $this->callback = $callback;
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
     * @return string[][]
     */
    public function getIntentClassMap(): array
    {
        return $this->intentClassMap;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }
}
