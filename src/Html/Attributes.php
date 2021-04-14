<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

class Attributes
{
    /** @var array<string,Attribute> map of attribute names to attributes */
    protected array $items = [];

    /**
     * Attributes constructor.
     *
     * @param array<string,Attribute|string|string[]>|null $attributes
     */
    public function __construct(array $attributes = null)
    {
        if ($attributes === null) {
            return;
        }

        foreach ($attributes as $key => $values) {
            if ($values instanceof Attribute) {
                $this->items[$values->getKey()] = $values;
            } else {
                $this->items[$key] = new Attribute($key, ...(array)$values);
            }
        }
    }

    /**
     * @param Attributes $attributes
     *
     * @return $this
     */
    public function merge(Attributes $attributes): self
    {
        return $this->mergeItems(...$attributes->getItems());
    }

    /**
     * @param Attribute ...$attributes
     *
     * @return $this
     */
    public function mergeItems(Attribute ...$attributes): self
    {
        foreach ($attributes as $attribute) {
            $key = $attribute->getKey();
            if (array_key_exists($key, $this->items)) {
                $this->items[$key]->append(...$attribute->getValues());
            } else {
                $this->items[$key] = $attribute;
            }
        }

        return $this;
    }

    /**
     * @param Attributes $attributes
     *
     * @return $this
     */
    public function replace(Attributes $attributes): self
    {
        return $this->replaceItems(...$attributes->getItems());
    }

    /**
     * @param Attribute ...$attributes
     *
     * @return $this
     */
    public function replaceItems(Attribute ...$attributes): self
    {
        foreach ($attributes as $attribute) {
            $key = $attribute->getKey();
            assert($key === $attribute->getKey());
            $this->items[$key] = $attribute;
        }

        return $this;
    }

    /**
     * @param string ...$keys
     *
     * @return $this
     */
    public function remove(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->items)) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    /**
     * @return Attribute[]
     */
    public function getItems(): array
    {
        return array_values($this->items);
    }

    /**
     * @param string $key
     *
     * @return Attribute|null
     */
    public function getItem(string $key): ?Attribute
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasItem(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     */
    public function mergeItem(Attribute $attribute): self
    {
        $key = $attribute->getKey();
        if (array_key_exists($key, $this->items) && !$this->items[$key]->isNull() && !$attribute->isNull()) {
            $this->items[$key]->append(...$attribute->getValues());
        } elseif (!$attribute->isNull() || !array_key_exists($key, $this->items)) {
            $this->items[$key] = $attribute;
        }

        return $this;
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     */
    public function replaceItem(Attribute $attribute): self
    {
        $this->items[$attribute->getKey()] = $attribute;

        return $this;
    }

    /**
     * @param Attributes|null $attributes
     *
     * @return bool
     */
    public function isEqual(?Attributes $attributes): bool
    {
        if (null === $attributes) {
            return count($this->items) === 0;
        }

        return (string)$this == (string)$attributes;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $return = '';
        foreach ($this->items as $item) {
            $return .= ' ' . (string)$item;
        }

        return $return;
    }
}
