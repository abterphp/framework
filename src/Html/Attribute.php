<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

class Attribute
{
    protected string $key;

    /** @var null|array<string,string> */
    protected ?array $values = null;

    /**
     * Attribute constructor.
     *
     * @param string                $key
     * @param string|float|int|bool ...$values
     */
    public function __construct(string $key, ...$values)
    {
        $this->key = $key;
        $this->append(...$values);
    }

    /**
     * @param string|float|int|bool ...$values
     *
     * @return $this
     */
    public function set(...$values): self
    {
        $this->values = [];

        return $this->unsafeSet(...$values);
    }

    /**
     * @param string|float|int|bool ...$values
     *
     * @return $this
     */
    public function append(...$values): self
    {
        if (!$values) {
            return $this;
        }

        if ($this->values === null) {
            $this->values = [];
        }

        return $this->unsafeSet(...$values);
    }

    /**
     * @param string|float|int|bool ...$values
     *
     * @return $this
     */
    private function unsafeSet(...$values): self
    {
        foreach ($values as $part) {
            if (empty($part)) {
                continue;
            }

            $part = (string)$part;

            $this->values[$part] = $part;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return is_null($this->values);
    }

    /**
     * @param string ...$value
     *
     * @return int
     */
    public function remove(string ...$value): int
    {
        $count = 0;
        foreach ($value as $key) {
            if (!array_key_exists($key, $this->values)) {
                continue;
            }
            unset($this->values[$key]);
            $count++;
        }

        return $count;
    }

    /**
     * @return $this
     */
    public function reset(): self
    {
        $this->values = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        if (null === $this->values) {
            return '';
        }

        return implode(' ', $this->values);
    }

    /**
     * @return string[]|null
     */
    public function getValues(): ?array
    {
        if (null === $this->values) {
            return null;
        }

        return array_values($this->values);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->values === null) {
            return $this->key;
        }

        return sprintf('%s="%s"', $this->key, implode(" ", $this->values));
    }
}
