<?php

namespace AbterPhp\Framework\Template;

class ParsedTemplate
{
    /** @var string */
    protected string $type;

    /** @var string */
    protected string $identifier;

    /** @var string[] */
    protected array $attributes;

    /** @var string[] */
    protected array $occurrences;

    /**
     * ParsedData constructor.
     *
     * @param string   $type
     * @param string   $identifier
     * @param string[] $attributes
     * @param string[] $occurences
     */
    public function __construct(string $type, string $identifier, array $attributes = [], array $occurences = [])
    {
        $this->type       = $type;
        $this->identifier = $identifier;
        $this->attributes = $attributes;
        $this->occurrences = $occurences;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string      $attribute
     * @param string|null $default
     *
     * @return string|null
     */
    public function getAttribute(string $attribute, ?string $default = null): ?string
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }

        return $default;
    }

    /**
     * @param string $occurrence
     *
     * @return ParsedTemplate
     */
    public function addOccurrence(string $occurrence): ParsedTemplate
    {
        $this->occurrences[] = $occurrence;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOccurrences(): array
    {
        return $this->occurrences;
    }
}
