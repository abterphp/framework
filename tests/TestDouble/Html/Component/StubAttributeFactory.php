<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble\Html\Component;

use AbterPhp\Framework\Html\Attributes;

class StubAttributeFactory
{
    public const ATTRIBUTE_FOO = 'foo';
    public const ATTRIBUTE_BAR = 'bar';

    public const VALUE_FOO     = 'foo';
    public const VALUE_BAZ     = 'baz';
    public const VALUE_BAR_BAZ = 'bar baz';

    /**
     * @param array<string,string[]> $extraAttributes
     *
     * @return Attributes
     */
    public static function createAttributes(array $extraAttributes = []): Attributes
    {
        $attributes = [
            static::ATTRIBUTE_FOO => [static::VALUE_FOO, static::VALUE_BAZ],
            static::ATTRIBUTE_BAR => [static::VALUE_BAR_BAZ],
        ];

        foreach ($extraAttributes as $k => $v) {
            if (!array_key_exists($k, $attributes)) {
                $attributes[$k] = $v;
                continue;
            }
            foreach ($v as $v2) {
                $attributes[$k][] = $v2;
            }
        }

        return new Attributes($attributes);
    }
}
