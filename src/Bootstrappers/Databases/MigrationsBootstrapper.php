<?php

namespace AbterPhp\Framework\Bootstrappers\Databases;

use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\FileMigrationFinder;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Databases\Migrations\Migrator;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Databases\Bootstrappers\MigrationBootstrapper as OpulenceMigrationsBootstrapper;
use Opulence\Framework\Databases\Migrations\ContainerMigrationResolver;
use Opulence\Ioc\IContainer;

class MigrationsBootstrapper extends OpulenceMigrationsBootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        global $abterModuleManager;

        $paths = Config::get('paths', 'database.migrations');

        $globalPaths = $paths ?: [];

        $abterPaths = $abterModuleManager->getMigrationPaths();

        $paths = array_merge($globalPaths, $abterPaths);

        $container->bindFactory(
            IMigrator::class,
            function () use ($container, $paths) {
                $fileMigrationFinder = new FileMigrationFinder();
                $resolver            = new ContainerMigrationResolver($container);

                $migrationPaths         = $fileMigrationFinder->findAll($paths);
                $connection             = $container->resolve(IConnection::class);
                $executedMigrationsRepo = $this->getExecutedMigrationRepository($container);

                return new Migrator($migrationPaths, $connection, $resolver, $executedMigrationsRepo);
            }
        );
    }
}
