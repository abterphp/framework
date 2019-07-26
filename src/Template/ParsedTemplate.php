<?php

namespace AbterPhp\Framework\Template;

class ParsedTemplate
{
    /** @var string */
    protected $type;

    /** @var string */
    protected $identifier;

    /** @var string[] */
    protected $attributes;

    /** @var string[] */
    protected $occurences;

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
        $this->occurences = $occurences;
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
     * @param string $attribute
     *
     * @return string|null
     */
    public function getAttribute(string $attribute): ?string
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }

        return null;
    }

    /**
     * @param string $occurence
     *
     * @return ParsedTemplate
     */
    public function addOccurence(string $occurence): ParsedTemplate
    {
        $this->occurences[] = $occurence;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOccurences(): array
    {
        return $this->occurences;
    }
}
