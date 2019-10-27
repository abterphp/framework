<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Priorities
{
    public const MINIMUM = PHP_INT_MIN;

    // Minus one million
    public const EXTREME_LOW = -1000000;

    // Minus ten thousand
    public const VERY_LOW = -10000;

    // Minus five thousand
    public const MODERATELY_LOW = -5000;

    // Minus two thousand
    public const SLIGHTLY_LOW = -2000;

    public const BELOW_ZERO = -500;

    public const ZERO = 0;

    public const BELOW_NORMAL = 500;

    public const NORMAL = 1000;

    // Two thousand
    public const SLIGHTLY_HIGH = 2000;

    // Five thousand
    public const MODERATELY_HIGH = 5000;

    // Ten thousand
    public const VERY_HIGH = 10000;

    // One million
    public const EXTREME_HIGH = 1000000;

    public const MAXIMUM = PHP_INT_MAX;
}
