<?php

namespace AbterPhp\Framework\Bootstrappers\Databases;

use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Databases\Bootstrappers\MigrationBootstrapper as OpulenceMigrationsBootstrapper;
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

        Config::set('paths', 'database.migrations', $paths);

        parent::registerBindings($container);
    }
}
