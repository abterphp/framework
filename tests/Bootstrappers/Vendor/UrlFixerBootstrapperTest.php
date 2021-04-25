<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Assets\UrlFixer;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class UrlFixerBootstrapperTest extends TestCase
{
    /** @var UrlFixerBootstrapper - System Under Test */
    protected UrlFixerBootstrapper $sut;

    public function setUp(): void
    {
        Environment::setVar(Env::MEDIA_BASE_URL, '?');

        $this->sut = new UrlFixerBootstrapper();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::MEDIA_BASE_URL);
    }

    public function testRegisterBindings(): void
    {
        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(UrlFixer::class);
        $this->assertInstanceOf(UrlFixer::class, $actual);
    }
}
