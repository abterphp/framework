<?php

namespace Vendor1\Library2;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER      => 'Vendor1\Library2',
    Module::DEPENDENCIES    => ['Src\Module1', 'Vendor1\Library1'],
    Module::ENABLED            => true,
    Module::BOOTSTRAPPERS   => [
        'Bootstrappers\Orm\OrmBootstrapper',
        'Bootstrappers\Validation\ValidatorBootstrapper',
    ],
    Module::COMMANDS        => [
        'Console\Commands\File\Cleanup',
    ],
    Module::ROUTE_PATHS     => [
        800 => [
            __DIR__ . '/routes.php',
        ],
    ],
    Module::EVENTS          => [
        Event::AUTH_READY            => [
            'Events\Listeners\AuthRegistrar@register',
        ],
        Event::TEMPLATE_ENGINE_READY => [
            'Events\Listeners\TemplateRegistrar@register',
        ],
        Event::NAVIGATION_READY      => [
            'Events\Listeners\NavigationRegistrar@register',
        ],
        Event::DASHBOARD_READY       => [
            'Events\Listeners\DashboardRegistrar@register',
        ],
    ],
    Module::MIGRATION_PATHS => [
        1000 => [
            realpath(__DIR__ . '/Databases/Migrations'),
        ],
    ],
];
