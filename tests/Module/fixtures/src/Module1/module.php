<?php

declare(strict_types=1);

namespace Src\Module1;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER         => 'Src\Module1',
    Module::DEPENDENCIES       => [],
    Module::ENABLED            => true,
    Module::BOOTSTRAPPERS      => [
        'Assets\Bootstrappers\AssetManagerBootstrapper',
        'Authorization\Bootstrappers\CacheManagerBootstrapper',
        'Cache\Bootstrappers\CacheBootstrapper',
        'Crypto\Bootstrappers\CryptoBootstrapper',
        'Databases\Bootstrappers\SqlBootstrapper',
        'Email\Bootstrappers\TransportBootstrapper',
        'Events\Bootstrappers\EventDispatcherBootstrapper',
        'Filesystem\Bootstrappers\FilesystemBootstrapper',
        'Http\Bootstrappers\LoggerBootstrapper',
        'Http\Bootstrappers\RouterBootstrapper',
        'Http\Bootstrappers\ViewBootstrapper',
        'Session\Bootstrappers\FlashServiceBootstrapper',
        'Template\Bootstrappers\CacheManagerBootstrapper',
        'Template\Bootstrappers\TemplateEngineBootstrapper',
    ],
    Module::CLI_BOOTSTRAPPERS  => [
        'Console\Bootstrappers\Commands\Cache\FlushCacheBootstrapper',
        'Console\Bootstrappers\Commands\Security\SecretGeneratorBootstrapper',
        'Databases\Bootstrappers\QueryFileLoaderBootstrapper',
        'Databases\Bootstrappers\MigrationsBootstrapper',
    ],
    Module::HTTP_BOOTSTRAPPERS => [
        'Authorization\Bootstrappers\EnforcerBootstrapper',
        'Dashboard\Bootstrappers\DashboardBootstrapper',
        'Grid\Bootstrappers\GridBootstrapper',
        'Http\Bootstrappers\SessionBootstrapper',
        'I18n\Bootstrappers\I18nBootstrapper',
        'Navigation\Bootstrappers\PrimaryBootstrapper',
        'Views\Bootstrappers\ViewFunctionsBootstrapper',
    ],
    Module::COMMANDS           => [
        'Assets\Command\FlushCache',
        'Authorization\Command\FlushCache',
        'Console\Commands\Cache\FlushCache',
        'Console\Commands\Security\SecretGenerator',
        'Template\Command\FlushCache',
    ],
    Module::EVENTS             => [
        Event::NAVIGATION_READY => [
            /** @see \AbterPhp\Framework\Events\Listeners\NavigationBuilder::register */
            'Events\Listeners\NavigationRegistrar@register',
        ],
    ],
    Module::MIDDLEWARE             => [
        1000 => [
            'Http\Middleware\EnvironmentWarning',
            'Http\Middleware\Session',
            'Http\Middleware\Security',
        ],
    ],
    Module::MIGRATION_PATHS    => [
        1000 => [
            realpath(__DIR__ . '/Databases/Migrations'),
        ],
    ],
];
