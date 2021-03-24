<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Databases;

use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Databases\Bootstrappers\MigrationBootstrapper as OpulenceMigrationsBootstrapper;
use Opulence\Ioc\IContainer;

class MigrationsBootstrapper extends OpulenceMigrationsBootstrapper
{
    protected ?array $migrationPaths = null;

    /**
     * @return array
     */
    public function getMigrationPaths(): array
    {
        global $abterModuleManager;

        if ($this->migrationPaths !== null) {
            return $this->migrationPaths;
        }

        $this->migrationPaths = $abterModuleManager->getMigrationPaths() ?: [];

        return $this->migrationPaths;
    }

    /**
     * @param array $migrationPaths
     *
     * @return $this
     */
    public function setMigrationPaths(array $migrationPaths): self
    {
        $this->migrationPaths = $migrationPaths;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        /** @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal */
        $globalPaths = (array)Config::get('paths', 'database.migrations', []);

        $modulePaths = $this->getMigrationPaths();

        $paths = array_merge($globalPaths, $modulePaths);

        Config::set('paths', 'database.migrations', $paths);

        parent::registerBindings($container);
    }
}
