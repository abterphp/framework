<?php

namespace Vendor2\Library1;

use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER   => 'Vendor2\Library2',
    Module::DEPENDENCIES => ['Vendor1\Library2'],
    Module::ENABLED            => true,
];
