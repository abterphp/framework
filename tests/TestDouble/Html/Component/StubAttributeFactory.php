<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble\Html\Component;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;

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
     * @return array<string,Attribute>
     */
    public static function createAttributes(array $extraAttributes = []): array
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

        return Attributes::fromArray($attributes);
    }
}
