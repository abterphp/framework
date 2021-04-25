<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @return array[]
     */
    public function toQueryProvider(): array
    {
        return [
            'empty'  => [[], ''],
            'simple' => [['foo' => 'Foo'], '?foo=Foo'],
            'unsafe' => [['foo' => 'Foo&Bar'], '?foo=Foo%26Bar'],
            'complex' => [['foo' => 'Foo&Bar', 'bar' => 'Bar=Baz'], '?foo=Foo%26Bar&bar=Bar%3DBaz'],
        ];
    }

    /**
     * @dataProvider toQueryProvider
     *
     * @param array  $parts
     * @param string $expectedResult
     */
    public function testToQuery(array $parts, string $expectedResult): void
    {
        $actualResult = Url::toQuery($parts);

        $this->assertSame($expectedResult, $actualResult);
    }
}
