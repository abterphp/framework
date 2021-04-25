<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Exception\Config;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Ioc\Container;
use PDOException;
use PHPUnit\Framework\TestCase;

class CasbinDatabaseAdapterBootstrapperTest extends TestCase
{
    /** @var CasbinDatabaseAdapterBootstrapper - System Under Test */
    protected CasbinDatabaseAdapterBootstrapper $sut;

    public function setUp(): void
    {
        Environment::unsetVar('DB_DRIVER');
        Environment::setVar('DB_HOST', 'db');
        Environment::setVar('DB_NAME', 'test');
        Environment::setVar('DB_PASSWORD', 'pass');
        Environment::setVar('DB_PORT', '4321');
        Environment::setVar('DB_USER', 'root');

        $this->sut = new CasbinDatabaseAdapterBootstrapper();
    }

    public function testRegisterBindingsPostgresTriesToConnectToDB(): void
    {
        Environment::setVar('DB_DRIVER', PostgreSqlDriver::class);

        $this->expectException(PDOException::class);

        $container = new Container();

        $this->sut->registerBindings($container);
    }

    public function testRegisterBindingsMySQLTriesToConnectToDB(): void
    {
        Environment::setVar('DB_DRIVER', MySqlDriver::class);

        $this->expectException(PDOException::class);

        $container = new Container();

        $this->sut->registerBindings($container);
    }

    public function testRegisterBindingsThrowsInvalidDriverException(): void
    {
        $dbDriverClassStub = 'FooClass';

        Environment::setVar('DB_DRIVER', $dbDriverClassStub);

        $this->expectException(Config::class);
        $this->expectErrorMessageMatches("/$dbDriverClassStub/");

        $container = new Container();

        $this->sut->registerBindings($container);
    }
}
