<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\Factory;

use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;
use PHPUnit\Framework\TestCase;

class MinifierTest extends TestCase
{
    /** @var Minifier - System Under Test */
    protected Minifier $sut;

    public function setUp(): void
    {
        $this->sut = new Minifier();

        parent::setUp();
    }

    public function testCreateCssMinifier(): void
    {
        $actualResult = $this->sut->createCssMinifier();

        $this->assertInstanceOf(CssMinifier::class, $actualResult);
    }

    public function testCreateJsMinifier(): void
    {
        $actualResult = $this->sut->createJsMinifier();

        $this->assertInstanceOf(JsMinifier::class, $actualResult);
    }
}
