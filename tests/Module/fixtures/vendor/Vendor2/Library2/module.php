<?php

namespace Vendor2\Library2;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER         => 'Vendor2\Library2',
    Module::DEPENDENCIES       => ['Vendor1\Library2'],
    Module::ENABLED            => true,
    Module::HTTP_BOOTSTRAPPERS => [
        'Bootstrappers\Http\Controllers\Website\ContactBootstrapper',
    ],
    Module::EVENTS             => [
        Event::TEMPLATE_ENGINE_READY => [
            'Events\Listeners\TemplateRegistrar@register',
        ],
        Event::DASHBOARD_READY       => [
            'Events\Listeners\DashboardRegistrar@register',
        ],
    ],
    Module::ROUTE_PATHS        => [
        2000 => [
            __DIR__ . '/routes.php',
        ],
    ],
    Module::MIGRATION_PATHS    => [
        1000 => [
            realpath(__DIR__ . '/Databases/Migrations'),
        ],
    ],
];
