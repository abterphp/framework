<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /** @var Url - System Under Test */
    protected Url $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Url();
    }

    /**
     * @return array[]
     */
    public function passesProvider(): array
    {
        return [
            'empty'      => ['', [], false],
            'url-http-1'    => ['http://example.com/', [], true],
            'url-http-2'    => ['http://example.com/foo-bar', [], true],
            'url-https-1'    => ['https://example.com/', [], true],
            'url-https-2'    => ['https://example.com/foo-bar', [], true],
            'missing-http'    => ['://example.com/foo-bar', [], false],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param bool  $expectedResult
     */
    public function testPasses($value, array $allValues, bool $expectedResult): void
    {
        $actualResult = $this->sut->passes($value, $allValues);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSlug(): void
    {
        $actualResult = $this->sut->getSlug();

        $this->assertSame('url', $actualResult);
    }
}
