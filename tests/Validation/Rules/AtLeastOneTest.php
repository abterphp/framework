<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use PHPUnit\Framework\TestCase;

class AtLeastOneTest extends TestCase
{
    /** @var AtLeastOne - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new AtLeastOne();
    }

    /**
     * @return array
     */
    public function passesProvider(): array
    {
        return [
            'empty-missing'     => ['', [], ['field2'], false],
            'empty-empty'       => ['', ['field2' => ''], ['field2'], false],
            'zero-missing'      => ['0', [], ['field2'], false],
            'zero-zero'         => ['0', ['field2' => '0'], ['field2'], false],
            'non-empty-missing' => ['foo', [], ['field2'], true],
            'non-empty-empty'   => ['foo', ['field2' => ''], ['field2'], true],
            'empty-non-empty'   => ['', ['field2' => 'foo'], ['field2'], true],
            'both-truthy'       => ['foo', ['field2' => 'bar'], ['field2'], true],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param array $args
     * @param bool  $expectedResult
     */
    public function testPasses($value, array $allValues, array $args, bool $expectedResult)
    {
        $this->sut->setArgs($args);

        $actualResult = $this->sut->passes($value, $allValues);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetErrorPlaceholders()
    {
        $this->sut->setArgs(['field2', 'field3']);

        $errorPlaceholders = $this->sut->getErrorPlaceholders();

        $this->assertEquals(['other1' => 'field2', 'other2' => 'field3'], $errorPlaceholders);
    }

    public function testSetArgsThrowsExceptionWithoutArgs()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sut->setArgs([]);
    }

    public function testSetArgsThrowsExceptionWithNonStringArgs()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sut->setArgs([123]);
    }

    public function testGetSlug()
    {
        $actualResult = $this->sut->getSlug();

        $this->assertSame('atLeastOne', $actualResult);
    }
}
