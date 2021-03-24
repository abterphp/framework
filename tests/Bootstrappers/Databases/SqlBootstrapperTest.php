<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Databases;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Databases\IConnection;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class SqlBootstrapperTest extends TestCase
{
    /** @var SqlBootstrapper - System Under Test */
    protected SqlBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new SqlBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ConnectionPool::class);
        $this->assertInstanceOf(ConnectionPool::class, $actual);

        $actual = $container->resolve(IConnection::class);
        $this->assertInstanceOf(IConnection::class, $actual);

        $actual = $container->resolve(TypeMapperFactory::class);
        $this->assertInstanceOf(TypeMapperFactory::class, $actual);
    }
}
