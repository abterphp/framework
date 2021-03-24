<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class MinifierFactoryBootstrapperTest extends TestCase
{
    /** @var MinifierFactoryBootstrapper - System Under Test */
    protected MinifierFactoryBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new MinifierFactoryBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(MinifierFactory::class);
        $this->assertInstanceOf(MinifierFactory::class, $actual);
    }
}
