<?php

namespace AbterPhp\Framework\Bootstrappers\Http;

use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Container;
use Opulence\Routing\Dispatchers\IRouteDispatcher;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\Caching\ICache;
use Opulence\Routing\Routes\Compilers\ICompiler;
use Opulence\Routing\Urls\UrlGenerator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class RouterBootstrapperTest extends TestCase
{
    private const ROOT_PATH = 'exampleDir';

    /** @var RouterBootstrapper */
    private RouterBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new RouterBootstrapper();
    }

    protected function tearDown(): void
    {
        Config::set('paths', 'config.http', null);
    }

    public function testRegisterBindings()
    {
        vfsStream::setup(static::ROOT_PATH);
        $exampleDir = vfsStream::url(static::ROOT_PATH);

        Config::set('paths', 'config.http', $exampleDir);

        file_put_contents($exampleDir . DIRECTORY_SEPARATOR . 'routes.php', '<?php return 1;');
        file_put_contents($exampleDir . DIRECTORY_SEPARATOR . 'module.php', '<?php return 2;');

        $this->sut->setRoutePaths([$exampleDir . DIRECTORY_SEPARATOR . 'module.php']);

        $container = new Container();

        $this->sut->registerBindings($container);

        $cache = $container->resolve(ICache::class);
        $this->assertInstanceOf(ICache::class, $cache);

        $routeDispatcher = $container->resolve(IRouteDispatcher::class);
        $this->assertInstanceOf(IRouteDispatcher::class, $routeDispatcher);

        $compiler = $container->resolve(ICompiler::class);
        $this->assertInstanceOf(ICompiler::class, $compiler);

        $router = $container->resolve(Router::class);
        $this->assertInstanceOf(Router::class, $router);

        $urlGenerator = $container->resolve(UrlGenerator::class);
        $this->assertInstanceOf(UrlGenerator::class, $urlGenerator);
    }
}
