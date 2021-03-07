<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Exception\Config;
use CasbinAdapter\Database\Adapter;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class CasbinDatabaseAdapterBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            Adapter::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws Config
     */
    public function registerBindings(IContainer $container)
    {
        $driverClass = Environment::getVar('DB_DRIVER', PostgreSqlDriver::class);

        switch ($driverClass) {
            case MySqlDriver::class:
                $dirDriver = 'mysql';
                break;
            case PostgreSqlDriver::class:
                $dirDriver = 'pgsql';
                break;
            default:
                throw new Config(
                    "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
                );
        }

        $config = [
            'type'     => $dirDriver,
            'hostname' => Environment::getVar(Env::DB_HOST),
            'database' => Environment::getVar(Env::DB_NAME),
            'username' => Environment::getVar(Env::DB_USER),
            'password' => Environment::getVar(Env::DB_PASSWORD),
            'hostport' => Environment::getVar(Env::DB_PORT),
        ];

        $adapter = Adapter::newAdapter($config);

        $container->bindInstance(Adapter::class, $adapter);
    }
}
