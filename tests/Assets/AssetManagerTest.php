<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AssetManagerTest extends TestCase
{
    const CSS_KEY = 'test-css';
    const JS_KEY  = 'test-js';

    const CSS_CONTENT = "body { \nbackground-color:  red;\n }";
    const JS_CONTENT  = "function (  ) { alert ('ok'   ); }\n ( ) ";

    const CSS_CONTENT_MINIFIED = 'body{background-color:red}';
    const JS_CONTENT_MINIFIED  = 'function(){alert(\'ok\')}()';

    const DIR_ROOT_JS    = '/tmp/js';
    const DIR_ROOT_CSS   = '/tmp/css';
    const DIR_CACHE_JS   = '/tmp/cache/js';
    const DIR_CACHE_CSS  = '/tmp/cache/css';
    const PATH_CACHE_JS  = '/path/js';
    const PATH_CACHE_CSS = '/path/css';

    /** @var AssetManager */
    protected $sut;

    /** @var CssMinifier|MockObject */
    protected $cssMinifier;

    /** @var JsMinifier|MockObject */
    protected $jsMinifier;

    /** @var MinifierFactory|MockObject */
    protected $minifierFactoryMock;

    public function setUp()
    {
        $this->cssMinifier = $this->getMockBuilder(CssMinifier::class)
            ->setMethods(['add', 'minify'])
            ->getMock();

        $this->jsMinifier = $this->getMockBuilder(JsMinifier::class)
            ->setMethods(['add', 'minify'])
            ->getMock();

        $this->minifierFactoryMock = $this->getMockBuilder(MinifierFactory::class)
            ->setMethods(['createCssMinifier', 'createJsMinifier'])
            ->getMock();

        $this->minifierFactoryMock->expects($this->any())->method('createCssMinifier')->willReturn($this->cssMinifier);
        $this->minifierFactoryMock->expects($this->any())->method('createJsMinifier')->willReturn($this->jsMinifier);

        $isCacheAllowed = false;

        $this->sut = new AssetManager(
            $this->minifierFactoryMock,
            static::DIR_ROOT_JS,
            static::DIR_ROOT_CSS,
            static::DIR_CACHE_JS,
            static::DIR_CACHE_CSS,
            static::PATH_CACHE_JS,
            static::PATH_CACHE_CSS,
            $isCacheAllowed
        );

        parent::setUp();
    }

    public function testAddJs()
    {
        $expected = static::JS_CONTENT_MINIFIED;
        $filename = 'hello.js';
        $fullPath = static::DIR_ROOT_JS . '/hello.js';

        $this->jsMinifier
            ->expects($this->once())
            ->method('add')
            ->with($fullPath);

        $this->jsMinifier
            ->expects($this->once())
            ->method('minify')
            ->willReturn(static::JS_CONTENT_MINIFIED);

        $this->sut->addJs(static::JS_KEY, $filename);

        $actual = $this->sut->renderJs(static::JS_KEY);

        $this->assertSame($expected, $actual);
    }

    public function testAddCss()
    {
        $expected = static::CSS_CONTENT_MINIFIED;
        $filename = 'hello.css';
        $fullPath = static::DIR_ROOT_CSS . '/hello.css';

        $this->cssMinifier
            ->expects($this->once())
            ->method('add')
            ->with($fullPath);

        $this->cssMinifier
            ->expects($this->once())
            ->method('minify')
            ->willReturn(static::CSS_CONTENT_MINIFIED);

        $this->sut->addCss(static::CSS_KEY, $filename);

        $actual = $this->sut->renderCss(static::CSS_KEY);

        $this->assertSame($expected, $actual);
    }

    public function testAddCssContent()
    {
        $expected = static::CSS_CONTENT_MINIFIED;

        $this->cssMinifier
            ->expects($this->once())
            ->method('add')
            ->with(static::CSS_CONTENT);

        $this->cssMinifier
            ->expects($this->once())
            ->method('minify')
            ->willReturn(static::CSS_CONTENT_MINIFIED);

        $this->sut->addCssContent(static::CSS_KEY, static::CSS_CONTENT);

        $actual = $this->sut->renderCss(static::CSS_KEY);

        $this->assertSame($expected, $actual);
    }

    public function testAddJsContent()
    {
        $expected = static::JS_CONTENT_MINIFIED;

        $this->jsMinifier
            ->expects($this->once())
            ->method('add')
            ->with(static::JS_CONTENT);

        $this->jsMinifier
            ->expects($this->once())
            ->method('minify')
            ->willReturn(static::JS_CONTENT_MINIFIED);

        $this->sut->addJsContent(static::JS_KEY, static::JS_CONTENT);

        $actual = $this->sut->renderJs(static::JS_KEY);

        $this->assertSame($expected, $actual);
    }

    public function testEnsureCssWebPathIsEmptyIfNoContentIsAddedForKey()
    {
        $actual = $this->sut->ensureCssWebPath(static::CSS_KEY);

        $this->assertEmpty($actual);
    }

    public function testEnsureCssWebPathBcd()
    {
        $expected = sprintf(
            '%s/%s%s',
            static::PATH_CACHE_CSS,
            static::CSS_KEY,
            AssetManager::FILE_EXTENSION_CSS
        );

        $this->sut->addCssContent(static::CSS_KEY, static::CSS_CONTENT);

        $actual = $this->sut->ensureCssWebPath(static::CSS_KEY);

        $this->assertContains($expected, $actual);
    }

    public function testEnsureJsWebPath()
    {
        $expected = sprintf(
            '%s/%s%s',
            static::PATH_CACHE_JS,
            static::JS_KEY,
            AssetManager::FILE_EXTENSION_JS
        );

        $this->sut->addJsContent(static::JS_KEY, static::JS_CONTENT);

        $actual = $this->sut->ensureJsWebPath(static::JS_KEY);

        $this->assertContains($expected, $actual);
    }
}
