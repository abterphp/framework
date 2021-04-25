<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Container;
use Opulence\Views\Caching\ArrayCache;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IO\IViewReader;
use Opulence\Views\Factories\IViewFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ViewBootstrapperTest extends TestCase
{
    protected const BASE_PATH = 'exampleDir';

    /** @var ViewBootstrapper - System Under Test */
    protected ViewBootstrapper $sut;

    protected vfsStreamDirectory $root;

    public function setUp(): void
    {
        Environment::unsetVar(Env::ENV_NAME);

        $this->root = vfsStream::setup(static::BASE_PATH);
        mkdir(vfsStream::url(static::BASE_PATH) . DIRECTORY_SEPARATOR . 'views');

        $this->sut = new ViewBootstrapper();
    }

    public function tearDown(): void
    {
        Config::set('views', 'cache', '');
        Config::set('views', 'cache.lifetime', '');
        Config::set('views', 'gc.chance', '');
        Config::set('views', 'gc.divisor', '');
        Config::set('paths', 'views.raw', '');
    }

    public function testRegisterBindingsDefault(): void
    {
        Environment::setVar(Env::ENV_NAME, 'foo');

        Config::set('paths', 'views.raw', '/tmp/dir-does-not-exist-ever-373292');
        Config::set('views', 'cache.lifetime', 0);
        Config::set('views', 'gc.chance', 0);
        Config::set('views', 'gc.divisor', 5);

        $resourcePaths = [vfsStream::url(static::BASE_PATH), '/tmp/dir-does-not-exist-ever-299182'];

        $this->sut->setResourcePaths($resourcePaths);

        $mockTranslator = $this->getMockBuilder(ITranslator::class)->getMock();

        $container = new Container();
        $container->bindInstance(ITranslator::class, $mockTranslator);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ICache::class);
        $this->assertInstanceOf(ICache::class, $actual);

        $actual = $container->resolve(ICompiler::class);
        $this->assertInstanceOf(ICompiler::class, $actual);

        $actual = $container->resolve(ITranspiler::class);
        $this->assertInstanceOf(ITranspiler::class, $actual);

        $actual = $container->resolve(IViewFactory::class);
        $this->assertInstanceOf(IViewFactory::class, $actual);

        $actual = $container->resolve(IViewReader::class);
        $this->assertInstanceOf(IViewReader::class, $actual);
    }

    public function testRegisterBindingsArrayCache(): void
    {
        Environment::setVar(Env::ENV_NAME, 'foo');

        Config::set('views', 'cache', ArrayCache::class);
        Config::set('paths', 'views.raw', '/tmp/dir-does-not-exist-ever-373292');

        $resourcePaths = [vfsStream::url(static::BASE_PATH), '/tmp/dir-does-not-exist-ever-299182'];

        $this->sut->setResourcePaths($resourcePaths);

        $mockTranslator = $this->getMockBuilder(ITranslator::class)->getMock();

        $container = new Container();
        $container->bindInstance(ITranslator::class, $mockTranslator);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ICache::class);
        $this->assertInstanceOf(ICache::class, $actual);

        $actual = $container->resolve(ICompiler::class);
        $this->assertInstanceOf(ICompiler::class, $actual);

        $actual = $container->resolve(ITranspiler::class);
        $this->assertInstanceOf(ITranspiler::class, $actual);

        $actual = $container->resolve(IViewFactory::class);
        $this->assertInstanceOf(IViewFactory::class, $actual);

        $actual = $container->resolve(IViewReader::class);
        $this->assertInstanceOf(IViewReader::class, $actual);
    }
}
