<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Authorization\CacheManager;
use AbterPhp\Framework\Authorization\CombinedAdapter;
use CasbinAdapter\Database\Adapter;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class CasbinCombinedAdapterBootstrapperTest extends TestCase
{
    /** @var CasbinCombinedAdapterBootstrapper - System Under Test */
    protected CasbinCombinedAdapterBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new CasbinCombinedAdapterBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $cacheManagerMock = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $adapterMock      = $this->getMockBuilder(Adapter::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(CacheManager::class, $cacheManagerMock);
        $container->bindInstance(Adapter::class, $adapterMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(CombinedAdapter::class);
        $this->assertInstanceOf(CombinedAdapter::class, $actual);
    }
}
