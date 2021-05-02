<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Helper\Debug;
use AbterPhp\Framework\Html\Attribute;

class Attributes
{
    /**
     * @param array<string,string|string[]>|null $values
     *
     * @return array
     */
    public static function fromArray(?array $values): array
    {
        if ($values === null) {
            return [];
        }

        $attributes = [];

        foreach ($values as $key => $value) {
            if (null === $value) {
                $values = [];
            } elseif (!is_array($value)) {
                $values = [$value];
            } else {
                $values = $value;
            }

            assert(Collection::allStrings($values), Debug::prettyPrint('not all strings: %s', $values));

            $attributes[$key] = new Attribute($key, ...$values);
        }

        return $attributes;
    }

    /**
     * @param array<string,Attribute>|null $attribA
     * @param array<string,Attribute>|null $attribB
     *
     * @return array<string,Attribute>
     */
    public static function merge(?array $attribA, ?array $attribB): array
    {
        $attribA ??= [];
        $attribB ??= [];

        if (count($attribB) === 0) {
            return $attribA;
        }

        foreach ($attribB as $key => $attrib2) {
            assert($attrib2 instanceof Attribute, Debug::prettyPrint('not an Attribute: "%s"', $attrib2));
            assert($key === $attrib2->getKey(), sprintf('key: %s <=> %s', $key, $attrib2->getKey()));

            $key = $attrib2->getKey();
            if (!array_key_exists($key, $attribA)) {
                $attribA[$key] = clone $attrib2;
                continue;
            }

            if (null !== $attrib2->getValues()) {
                $attribA[$key] = $attribA[$key]->append(...$attrib2->getValues());
            }
        }

        return $attribA;
    }

    /**
     * @param array<string,Attribute>|null $attribA
     * @param Attribute                    ...$attribB
     *
     * @return array<string,Attribute>
     */
    public static function mergeItem(?array $attribA, Attribute ...$attribB): array
    {
        $attribC = [];
        foreach ($attribB as $attribute) {
            $attribC[$attribute->getKey()] = $attribute;
        }

        return static::merge($attribA, $attribC);
    }

    /**
     * @param array<string,Attribute>|null $attribA
     * @param array<string,Attribute>|null $attribB
     *
     * @return array<string,Attribute>
     */
    public static function replace(?array $attribA, ?array $attribB): array
    {
        $attribA ??= [];
        $attribB ??= [];

        if (count($attribB) === 0) {
            return $attribA;
        }

        foreach ($attribB as $key => $attrib2) {
            assert($attrib2 instanceof Attribute, Debug::prettyPrint('not an Attribute: "%s"', $attrib2));
            assert($key === $attrib2->getKey(), sprintf('key: %s <=> %s', $key, $attrib2->getKey()));

            $key           = $attrib2->getKey();
            $attribA[$key] = clone $attrib2;
        }

        return $attribA;
    }

    /**
     * @param array<string,Attribute>|null $attribA
     * @param Attribute                    ...$attribB
     *
     * @return array<string,Attribute>
     */
    public static function replaceItem(?array $attribA, Attribute ...$attribB): array
    {
        $attribC = [];
        foreach ($attribB as $attribute) {
            $attribC[$attribute->getKey()] = $attribute;
        }

        return static::replace($attribA, $attribC);
    }

    /**
     * @param array<string,Attribute>|null $attributes
     * @param array<string,Attribute>|null $attributes2
     *
     * @return bool
     */
    public static function isEqual(?array $attributes, ?array $attributes2): bool
    {
        $attributes  ??= [];
        $attributes2 ??= [];

        if (count($attributes) != count($attributes2)) {
            return false;
        }

        foreach ($attributes as $key => $attribute) {
            if (!array_key_exists($key, $attributes2)) {
                return false;
            }

            if (!$attribute->isEqual($attributes2[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string,Attribute> $attributes
     *
     * @return string
     */
    public static function toString(array $attributes): string
    {
        $attr = [];
        foreach ($attributes as $attribute) {
            $attr[] = " " . $attribute;
        }

        return join('', $attr);
    }
}
