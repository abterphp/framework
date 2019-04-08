<?php

namespace AbterPhp\Framework\Bootstrappers\Databases;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Databases\QueryFileLoader;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class QueryFileLoaderBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            QueryFileLoader::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $dirMigrations = getenv(Env::DIR_MIGRATIONS);
        $driverClass   = getenv(Env::DB_DRIVER) ?: PostgreSqlDriver::class;

        switch ($driverClass) {
            case MySqlDriver::class:
                $dirDriver = 'mysql';
                break;
            case PostgreSqlDriver::class:
                $dirDriver = 'pgsql';
                break;
            default:
                throw new \RuntimeException(
                    "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
                );
        }

        $queryFileLoader = new QueryFileLoader($dirMigrations, $dirDriver);

        $container->bindInstance(QueryFileLoader::class, $queryFileLoader);
    }
}
