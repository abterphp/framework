<?php

declare(strict_types=1);

namespace Vendor1\Library1;

use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER   => 'Vendor1\Library1',
    Module::DEPENDENCIES => ['Vendor2\Library2'],
    Module::ENABLED      => true,
];
