<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use PHPUnit\Framework\TestCase;

class Base64Test extends TestCase
{
    /** @var Base64 - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Base64();
    }

    /**
     * @return array
     */
    public function passesProvider(): array
    {
        return [
            'empty' => ['', [], true],
            'basic' => ['YXNkYXNk', [], true],
            'wrong' => ['https://www.example.com/', [], false],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param bool  $expectedResult
     */
    public function testPasses($value, array $allValues, bool $expectedResult)
    {
        $actualResult = $this->sut->passes($value, $allValues);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSlug()
    {
        $actualResult = $this->sut->getSlug();

        $this->assertSame('base64', $actualResult);
    }
}
