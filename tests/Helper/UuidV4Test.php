<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class UuidV4Test extends TestCase
{
    /**
     * Matches Uuid's versions 1 to 5.
     */
    protected const REGEX_UUID = '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/';

    public function testFormat(): void
    {
        $actualResult = UuidV4::generate();

        $this->assertMatchesRegularExpression(static::REGEX_UUID, $actualResult);
    }
}
