<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    protected const DEFAULT_PAGE_SIZE = 5;
    protected const PAGE_SIZE_OPTIONS = [5, 10, 25];
    protected const NUMBER_COUNT = 33;

    /** @var Options - System Under Test */
    protected Options $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Options(static::DEFAULT_PAGE_SIZE, static::PAGE_SIZE_OPTIONS, static::NUMBER_COUNT);
    }

    public function testGetDefaultPageSize(): void
    {
        $actualResult = $this->sut->getDefaultPageSize();

        $this->assertSame(static::DEFAULT_PAGE_SIZE, $actualResult);
    }

    public function testGetPageSizeOptions(): void
    {
        $actualResult = $this->sut->getPageSizeOptions();

        $this->assertSame(static::PAGE_SIZE_OPTIONS, $actualResult);
    }

    public function testGetNumberCount(): void
    {
        $actualResult = $this->sut->getNumberCount();

        $this->assertSame(static::NUMBER_COUNT, $actualResult);
    }

    public function testGetAttributesEmptyByDefault(): void
    {
        $actualResult = $this->sut->getAttributes();

        $this->assertSame([], $actualResult);
    }

    public function testGetAttributes(): void
    {
        $attributes = ['foo' => 'bar'];

        $this->sut->setAttributes($attributes);

        $actualResult = $this->sut->getAttributes();

        $this->assertSame($attributes, $actualResult);
    }
}
