<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Environments;

use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    private const KEY = "FOOBAR001";

    public function tearDown(): void
    {
        Environment::unsetVar(static::KEY);
    }

    public function testUnset(): void
    {
        $value = static::KEY;

        Environment::setVar(static::KEY, $value);
        $this->assertEquals($value, Environment::getVar(static::KEY));

        Environment::unsetVar(static::KEY);

        $this->assertNull(Environment::getVar(static::KEY));
    }

    public function testMustGetVarGetsDefaultIfEnvironmentVariableIsMissing(): void
    {
        $value = static::KEY;

        $this->assertEquals($value, Environment::mustGetVar(static::KEY, $value));
    }

    public function testMustGetVarGetsEnvironmentVariableValueByDefault(): void
    {
        $value = static::KEY;
        $wrong = "BARBAZ002";

        Environment::setVar(static::KEY, $value);

        $this->assertEquals($value, Environment::mustGetVar(static::KEY, $wrong));
    }

    public function testMustGetVarThrowsExceptionIfEnvironmentVariableAndDefaultAreMissing(): void
    {
        $this->expectException(\RuntimeException::class);

        Environment::mustGetVar(static::KEY);
    }
}
