<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Html\Attribute;

class ParsedTemplate
{
    protected string $type;

    protected string $identifier;

    /** @var array<string,Attribute> */
    protected array $attributes;

    /** @var string[] */
    protected array $occurrences;

    /**
     * ParsedData constructor.
     *
     * @param string                       $type
     * @param string                       $identifier
     * @param array<string,Attribute>|null $attributes
     * @param string[]                     $occurrences
     */
    public function __construct(
        string $type,
        string $identifier,
        ?array $attributes = null,
        array $occurrences = []
    ) {
        $this->type        = $type;
        $this->identifier  = $identifier;
        $this->attributes  = $attributes ?? [];
        $this->occurrences = $occurrences;
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
     * @return array<string,Attribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string      $key
     * @param string|null $default
     *
     * @return string|null
     */
    public function getAttributeValue(string $key, ?string $default = null): ?string
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key]->getValue();
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
