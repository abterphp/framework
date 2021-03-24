<?php

declare(strict_types=1);

namespace Vendor2\Library2;

use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER   => 'Vendor2\Library2',
    Module::DEPENDENCIES => ['Vendor2\Library1'],
    Module::ENABLED            => true,
];
