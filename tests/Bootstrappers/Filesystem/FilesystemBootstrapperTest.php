<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Filesystem;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Filesystem\Uploader;
use League\Flysystem\Filesystem;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class FilesystemBootstrapperTest extends TestCase
{
    /** @var FilesystemBootstrapper - System Under Test */
    protected FilesystemBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new FilesystemBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::DIR_PRIVATE);
    }

    public function testRegisterBindings(): void
    {
        Environment::setVar(Env::DIR_PRIVATE, '/tmp/bar');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Filesystem::class);
        $this->assertInstanceOf(Filesystem::class, $actual);

        $actual = $container->resolve(Uploader::class);
        $this->assertInstanceOf(Uploader::class, $actual);
    }
}
