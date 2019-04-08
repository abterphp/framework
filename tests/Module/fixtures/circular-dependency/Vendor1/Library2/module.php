<?php

namespace Vendor1\Library2;

use AbterPhp\Framework\Constant\Module;

return [
    Module::IDENTIFIER   => 'Vendor1\Library2',
    Module::DEPENDENCIES => ['Vendor1\Library1'],
    Module::ENABLED      => true,
];
