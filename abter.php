<?php

use AbterPhp\Framework\Bootstrappers;
use AbterPhp\Framework\Console;
use AbterPhp\Framework\Constant\Module;
use AbterPhp\Framework\Constant\Priorities;
use AbterPhp\Framework\Http;

return [
    Module::IDENTIFIER         => 'AbterPhp\Framework',
    Module::DEPENDENCIES       => [],
    Module::ENABLED            => true,
    Module::BOOTSTRAPPERS      => [
        Bootstrappers\Assets\AssetManagerBootstrapper::class,
        Bootstrappers\Authorization\CacheManagerBootstrapper::class,
        Bootstrappers\Cache\CacheBootstrapper::class,
        Bootstrappers\Crypto\CryptoBootstrapper::class,
        Bootstrappers\Databases\SqlBootstrapper::class,
        Bootstrappers\Email\SenderBootstrapper::class,
        Bootstrappers\Email\TransportBootstrapper::class,
        Bootstrappers\Events\EventDispatcherBootstrapper::class,
        Bootstrappers\Filesystem\FilesystemBootstrapper::class,
        Bootstrappers\Http\LoggerBootstrapper::class,
        Bootstrappers\Http\RouterBootstrapper::class,
        Bootstrappers\Http\ViewBootstrapper::class,
        Bootstrappers\Session\FlashServiceBootstrapper::class,
        Bootstrappers\Template\CacheManagerBootstrapper::class,
        Bootstrappers\Template\EngineBootstrapper::class,
    ],
    Module::CLI_BOOTSTRAPPERS  => [
        Bootstrappers\Console\Commands\Cache\FlushCacheBootstrapper::class,
        Bootstrappers\Console\Commands\Security\SecretGeneratorBootstrapper::class,
        Bootstrappers\Databases\QueryFileLoaderBootstrapper::class,
        Bootstrappers\Databases\MigrationsBootstrapper::class,
    ],
    Module::HTTP_BOOTSTRAPPERS => [
        Bootstrappers\Authorization\EnforcerBootstrapper::class,
        Bootstrappers\Dashboard\DashboardBootstrapper::class,
        Bootstrappers\Grid\GridBootstrapper::class,
        Bootstrappers\Http\SessionBootstrapper::class,
        Bootstrappers\Http\Views\BuildersBootstrapper::class,
        Bootstrappers\I18n\I18nBootstrapper::class,
        Bootstrappers\Navigation\NavigationBootstrapper::class,
        Bootstrappers\Views\ViewFunctionsBootstrapper::class,
    ],
    Module::COMMANDS           => [
        Console\Commands\Assets\FlushCache::class,
        Console\Commands\Authorization\FlushCache::class,
        Console\Commands\Cache\FlushCache::class,
        Console\Commands\Security\SecretGenerator::class,
        Console\Commands\Template\FlushCache::class,
    ],
    Module::MIDDLEWARE         => [
        Priorities::NORMAL => [
            Http\Middleware\EnvironmentWarning::class,
            Http\Middleware\Session::class,
            Http\Middleware\Security::class,
        ],
    ],
    Module::ROUTE_PATHS        => [
        // Fallback routes, should be easy to override
        Priorities::EXTREME_LOW   => [
            __DIR__ . '/fallback-routes.php',
        ],
        // Important routes
        Priorities::SLIGHTLY_HIGH => [
            __DIR__ . '/asset-routes.php',
        ],
    ],
    Module::RESOURCE_PATH    => realpath(__DIR__ . '/resources'),
];
