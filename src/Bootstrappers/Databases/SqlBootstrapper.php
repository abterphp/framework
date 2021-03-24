<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Databases;

use AbterPhp\Framework\Constant\Env;
use Opulence\Databases\Adapters\Pdo\MySql\Driver;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Databases\ConnectionPools\SingleServerConnectionPool;
use Opulence\Databases\IConnection;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\Databases\Server;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines the SQL bootstrapper
 */
class SqlBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [ConnectionPool::class, IConnection::class, TypeMapperFactory::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $connectionPool = new SingleServerConnectionPool(
            new Driver(),
            new Server(
                Environment::getVar(Env::DB_HOST),
                Environment::getVar(Env::DB_USER),
                Environment::getVar(Env::DB_PASSWORD),
                Environment::getVar(Env::DB_NAME),
                (int)Environment::getVar(Env::DB_PORT)
            )
        );
        $container->bindInstance(ConnectionPool::class, $connectionPool);
        $container->bindInstance(IConnection::class, $connectionPool->getWriteConnection());
        $container->bindInstance(TypeMapperFactory::class, new TypeMapperFactory());
    }
}
