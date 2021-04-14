<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Html\Attributes;

class ParsedTemplate
{
    protected string $type;

    protected string $identifier;

    protected Attributes $attributes;

    /** @var string[] */
    protected array $occurrences;

    /**
     * ParsedData constructor.
     *
     * @param string          $type
     * @param string          $identifier
     * @param Attributes|null $attributes
     * @param string[]        $occurrences
     */
    public function __construct(
        string $type,
        string $identifier,
        ?Attributes $attributes = null,
        array $occurrences = []
    ) {
        $this->type        = $type;
        $this->identifier  = $identifier;
        $this->attributes  = $attributes ?? new Attributes();
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
     * @return Attributes
     */
    public function getAttributes(): Attributes
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
        $attribute = $this->attributes->getItem($key);
        if ($attribute && !$attribute->isNull()) {
            return $attribute->getValue();
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
