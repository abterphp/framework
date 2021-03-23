<?php

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Assets\UrlFixer;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class UrlFixerBootstrapperTest extends TestCase
{
    /** @var UrlFixerBootstrapper - System Under Test */
    protected UrlFixerBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new UrlFixerBootstrapper();
    }

    public function testRegisterBindings()
    {
        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(UrlFixer::class);
        $this->assertInstanceOf(UrlFixer::class, $actual);
    }
}
