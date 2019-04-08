<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Priorities
{
    const MINIMUM = PHP_INT_MIN;

    // Minus one million
    const EXTREME_LOW = -1000000;

    // Minus ten thousand
    const VERY_LOW = -10000;

    // Minus five thousand
    const MODERATELY_LOW = -5000;

    // Minus two thousand
    const SLIGHTLY_LOW = -2000;

    const BELOW_ZERO = -500;

    const ZERO = 0;

    const BELOW_NORMAL = 500;

    const NORMAL = 1000;

    // Two thousand
    const SLIGHTLY_HIGH = 2000;

    // Five thousand
    const MODERATELY_HIGH = 5000;

    // Ten thousand
    const VERY_HIGH = 10000;

    // One million
    const EXTREME_HIGH = 1000000;

    const MAXIMUM = PHP_INT_MAX;
}
