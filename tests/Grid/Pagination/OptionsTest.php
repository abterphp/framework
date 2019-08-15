<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    /** @var Options - System Under Test */
    protected $sut;

    /** @var int */
    protected $defaultPageSize = 5;

    /** @var array */
    protected $pageSizeOptions = [5, 10, 25];

    /** @var int */
    protected $numberCount = 33;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Options($this->defaultPageSize, $this->pageSizeOptions, $this->numberCount);
    }

    public function testGetDefaultPageSize()
    {
        $actualResult = $this->sut->getDefaultPageSize();

        $this->assertSame($this->defaultPageSize, $actualResult);
    }

    public function testGetPageSizeOptions()
    {
        $actualResult = $this->sut->getPageSizeOptions();

        $this->assertSame($this->pageSizeOptions, $actualResult);
    }

    public function testGetNumberCount()
    {
        $actualResult = $this->sut->getNumberCount();

        $this->assertSame($this->numberCount, $actualResult);
    }

    public function testGetAttributesEmptyByDefault()
    {
        $actualResult = $this->sut->getAttributes();

        $this->assertSame([], $actualResult);
    }

    public function testGetAttributes()
    {
        $attributes = ['foo' => 'bar'];

        $this->sut->setAttributes($attributes);

        $actualResult = $this->sut->getAttributes();

        $this->assertSame($attributes, $actualResult);
    }
}
