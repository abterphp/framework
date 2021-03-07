<?php

namespace AbterPhp\Framework\Bootstrappers\Assets;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Assets\UrlFixer;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Filesystem\FileFinder;
use Opulence\Ioc\Container;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AssetManagerBootstrapperTest extends TestCase
{
    protected AssetManagerBootstrapper $sut;

    /** @var ITranspiler|MockObject|MockObject */
    protected $transpilerMock;

    public function setUp(): void
    {
        Environment::setVar(Env::DIR_MEDIA, '');
        Environment::setVar(Env::CACHE_BASE_PATH, '');
        Environment::setVar(Env::ENV_NAME, 'foo');

        $this->sut = new AssetManagerBootstrapper();

        $this->transpilerMock = $this->getMockBuilder(ITranspiler::class)->getMock();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::DIR_MEDIA);
        Environment::unsetVar(Env::CACHE_BASE_PATH);
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function testRegisterBindingsBindsAnAssetManager()
    {
        $fileFinderMock   = $this->getMockBuilder(FileFinder::class)->getMock();
        $cacheManagerMock = $this->getMockBuilder(ICacheManager::class)->getMock();
        $urlFixerMock     = $this->getMockBuilder(UrlFixer::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(FileFinder::class, $fileFinderMock);
        $container->bindInstance(ICacheManager::class, $cacheManagerMock);
        $container->bindInstance(UrlFixer::class, $urlFixerMock);
        $container->bindInstance(ITranspiler::class, $this->transpilerMock);

        $this->transpilerMock->expects($this->exactly(3))->method('registerViewFunction');

        $this->sut->registerBindings($container);

        $actual = $container->resolve(AssetManager::class);
        $this->assertInstanceOf(AssetManager::class, $actual);
    }

    public function testCreateAssetJsViewFunctionSimple()
    {
        $keyStub     = 'foo';
        $webPathStub = 'baz';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock->expects($this->once())->method('ensureJsWebPath')->with($keyStub)->willReturn($webPathStub);

        $actual = $this->sut->createAssetJsViewFunction($assetManagerMock)([$keyStub]);
        $this->assertSame("<script src=\"$webPathStub\"></script>\n", $actual);
    }

    public function testCreateAssetJsViewFunctionEmpty()
    {
        $keyStub     = 'foo';
        $webPathStub = '';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock->expects($this->once())->method('ensureJsWebPath')->with($keyStub)->willReturn($webPathStub);

        $actual = $this->sut->createAssetJsViewFunction($assetManagerMock)([$keyStub]);
        $this->assertSame('', $actual);
    }

    public function testCreateAssetJsViewFunctionCustomType()
    {
        $keyStub     = 'foo';
        $typeStub    = 'bar';
        $webPathStub = 'baz';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock->expects($this->once())->method('ensureJsWebPath')->with($keyStub)->willReturn($webPathStub);

        $actual = $this->sut->createAssetJsViewFunction($assetManagerMock)([$keyStub], $typeStub);
        $this->assertSame("<script type=\"$typeStub\" src=\"$webPathStub\"></script>\n", $actual);
    }

    public function testCreateAssetJsViewFunctionMultiple()
    {
        $keyStub1     = 'foo';
        $keyStub2     = 'bar';
        $webPathStub1 = 'baz';
        $webPathStub2 = 'quix';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock
            ->expects($this->exactly(2))
            ->method('ensureJsWebPath')
            ->withConsecutive([$keyStub1], [$keyStub2])
            ->willReturnOnConsecutiveCalls($webPathStub1, $webPathStub2);

        $actual = $this->sut->createAssetJsViewFunction($assetManagerMock)([$keyStub1, $keyStub2]);
        $this->assertStringContainsString("<script src=\"$webPathStub1\"></script>", $actual);
        $this->assertStringContainsString("<script src=\"$webPathStub2\"></script>", $actual);
    }

    public function testCreateAssetCssViewFunctionSimple()
    {
        $keyStub     = 'foo';
        $webPathStub = 'baz';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock->expects($this->once())->method('ensureCssWebPath')->with($keyStub)->willReturn($webPathStub);

        $actual = $this->sut->createAssetCssViewFunction($assetManagerMock)([$keyStub]);
        $this->assertSame("<link href=\"$webPathStub\" rel=\"stylesheet\">\n", $actual);
    }

    public function testCreateAssetCssViewFunctionEmpty()
    {
        $keyStub     = 'foo';
        $webPathStub = '';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock->expects($this->once())->method('ensureCssWebPath')->with($keyStub)->willReturn($webPathStub);

        $actual = $this->sut->createAssetCssViewFunction($assetManagerMock)([$keyStub]);
        $this->assertSame('', $actual);
    }

    public function testCreateAssetCssViewFunctionMultiple()
    {
        $keyStub1     = 'foo';
        $keyStub2     = 'bar';
        $webPathStub1 = 'baz';
        $webPathStub2 = 'quix';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock
            ->expects($this->exactly(2))
            ->method('ensureCssWebPath')
            ->withConsecutive([$keyStub1], [$keyStub2])
            ->willReturnOnConsecutiveCalls($webPathStub1, $webPathStub2);

        $actual = $this->sut->createAssetCssViewFunction($assetManagerMock)([$keyStub1, $keyStub2]);
        $this->assertStringContainsString("<link href=\"$webPathStub1\" rel=\"stylesheet\">", $actual);
        $this->assertStringContainsString("<link href=\"$webPathStub2\" rel=\"stylesheet\">", $actual);
    }

    public function testCreateAssetImgViewFunction()
    {
        $keyStub     = 'foo';
        $altStub     = 'bar';
        $webPathStub = 'baz';

        $assetManagerMock = $this->getMockBuilder(AssetManager::class)->disableOriginalConstructor()->getMock();
        $assetManagerMock->expects($this->once())->method('ensureImgWebPath')->with($keyStub)->willReturn($webPathStub);

        $actual = $this->sut->createAssetImgViewFunction($assetManagerMock)($keyStub, $altStub);
        $this->assertSame("<img src=\"$webPathStub\" alt=\"$altStub\">\n", $actual);
    }
}
