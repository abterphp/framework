<?php

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
    /** @var CasbinDatabaseAdapterBootstrapper */
    protected CasbinDatabaseAdapterBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new CasbinDatabaseAdapterBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar('DB_DRIVER');
    }

    public function testRegisterBindingsPostgresTriesToConnectToDB()
    {
        Environment::setVar('DB_DRIVER', PostgreSqlDriver::class);

        $this->expectException(PDOException::class);
        $this->expectErrorMessageMatches('/No such file or directory/');

        $container = new Container();

        $this->sut->registerBindings($container);
    }

    public function testRegisterBindingsMySQLTriesToConnectToDB()
    {
        Environment::setVar('DB_DRIVER', MySqlDriver::class);

        $this->expectException(PDOException::class);
        $this->expectErrorMessageMatches('/No such file or directory/');

        $container = new Container();

        $this->sut->registerBindings($container);
    }

    public function testRegisterBindingsThrowsInvalidDriverException()
    {
        $dbDriverClassStub = 'FooClass';

        Environment::setVar('DB_DRIVER', $dbDriverClassStub);

        $this->expectException(Config::class);
        $this->expectErrorMessageMatches("/$dbDriverClassStub/");

        $container = new Container();

        $this->sut->registerBindings($container);
    }
}
