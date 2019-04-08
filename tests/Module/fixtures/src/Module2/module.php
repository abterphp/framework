<?php

namespace Src\Module2;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER         => 'Src\Module2',
    Module::DEPENDENCIES       => ['Src\Module1'],
    Module::ENABLED            => true,
    Module::BOOTSTRAPPERS      => [
        'Bootstrappers\Orm\OrmBootstrapper',
        'Bootstrappers\Validation\ValidatorBootstrapper',
    ],
    Module::HTTP_BOOTSTRAPPERS => [
        'Bootstrappers\Http\Controllers\Website\IndexBootstrapper',
        'Bootstrappers\Http\Views\BuildersBootstrapper',
    ],
    Module::EVENTS             => [
        Event::TEMPLATE_ENGINE_READY => [
            'Events\Listeners\TemplateRegistrar@register',
        ],
        Event::NAVIGATION_READY      => [
            'Events\Listeners\NavigationRegistrar@register',
        ],
        Event::ENTITY_POST_CHANGE    => [
            'Events\Listeners\PageInvalidator@register',
        ],
        Event::DASHBOARD_READY       => [
            'Events\Listeners\DashboardRegistrar@register',
        ],
    ],
    Module::ROUTE_PATHS        => [
        50000 => [
            __DIR__ . '/routes.php',
        ],
    ],
    Module::MIGRATION_PATHS    => [
        1000 => [
            realpath(__DIR__ . '/Databases/Migrations'),
        ],
    ],
];
