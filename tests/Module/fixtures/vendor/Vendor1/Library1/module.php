<?php

namespace Vendor1\Library1;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER         => 'Vendor1\Library1',
    Module::DEPENDENCIES       => ['Src\Module1'],
    Module::ENABLED            => true,
    Module::BOOTSTRAPPERS      => [
        'Bootstrappers\Orm\OrmBootstrapper',
        'Bootstrappers\Validation\ValidatorBootstrapper',
    ],
    Module::CLI_BOOTSTRAPPERS  => [
        'Bootstrappers\Console\Commands\CommandsBootstrapper',
    ],
    Module::HTTP_BOOTSTRAPPERS => [
        'Bootstrappers\Http\Controllers\Execute\LoginBootstrapper',
        'Bootstrappers\Http\Controllers\Form\LoginBootstrapper',
        'Bootstrappers\Http\Controllers\Form\UserBootstrapper',
        'Bootstrappers\Http\Views\BuildersBootstrapper',
        'Bootstrappers\Vendor\SlugifyBootstrapper',
    ],
    Module::COMMANDS           => [
        'Console\Commands\User\Create',
        'Console\Commands\User\Delete',
        'Console\Commands\User\UpdatePassword',
        'Console\Commands\UserGroup\Display',
    ],
    Module::EVENTS             => [
        Event::AUTH_READY         => [
            'Events\Listeners\AuthRegistrar@register',
        ],
        Event::NAVIGATION_READY   => [
            'Events\Listeners\NavigationRegistrar@register',
        ],
        Event::ENTITY_POST_CHANGE => [
            'Events\Listeners\AuthInvalidator@register',
        ],
        Event::DASHBOARD_READY    => [
            'Events\Listeners\DashboardRegistrar@register',
        ],
    ],
    Module::MIDDLEWARE         => [
        1000 => [
            'Http\Middleware\CheckCsrfToken',
            'Http\Middleware\Security',
        ],
    ],
    Module::ROUTE_PATHS        => [
        1000 => [
            __DIR__ . '/routes.php',
        ],
    ],
    Module::MIGRATION_PATHS    => [
        1000 => [
            realpath(__DIR__ . '/Databases/Migrations'),
        ],
    ],
];
