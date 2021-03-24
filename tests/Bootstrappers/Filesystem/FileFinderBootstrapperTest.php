<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Filesystem;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Filesystem\FileFinder;
use AbterPhp\Framework\Filesystem\IFileFinder;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class FileFinderBootstrapperTest extends TestCase
{
    /** @var FileFinderBootstrapper - System Under Test */
    protected FileFinderBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new FileFinderBootstrapper();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::DIR_PUBLIC);
    }

    public function testRegisterBindings(): void
    {
        Environment::setVar(Env::DIR_PUBLIC, '/tmp/foo');

        $assetsPaths = ['foo' => ['/tmp/bar'], 'bar' => ['']];

        $this->sut->setAssetPaths($assetsPaths);

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(FileFinder::class);
        $this->assertInstanceOf(FileFinder::class, $actual);

        $actual = $container->resolve(IFileFinder::class);
        $this->assertInstanceOf(IFileFinder::class, $actual);
    }
}
