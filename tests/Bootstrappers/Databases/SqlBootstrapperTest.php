<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Databases;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
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
        Environment::unsetVar(Env::DB_DRIVER);
        Environment::setVar(Env::DB_HOST, 'db');
        Environment::setVar(Env::DB_NAME, 'test');
        Environment::setVar(Env::DB_PORT, '4321');
        Environment::setVar(Env::DB_USER, 'root');
        Environment::setVar(Env::DB_PASSWORD, 'pass');
        $this->sut = new SqlBootstrapper();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::DB_HOST);
        Environment::unsetVar(Env::DB_NAME);
        Environment::unsetVar(Env::DB_PORT);
        Environment::unsetVar(Env::DB_USER);
        Environment::unsetVar(Env::DB_PASSWORD);
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
