<?php

namespace Vendor2\Library1;

use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER         => 'Vendor2\Library1',
    Module::DEPENDENCIES       => ['Src\Module1'],
    Module::ENABLED            => true,
    Module::HTTP_BOOTSTRAPPERS => [
        'Events\Bootstrappers\Listeners',
    ],
    Module::EVENTS             => [],
];
