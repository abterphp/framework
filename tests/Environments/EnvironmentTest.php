<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Environments;

use AbterPhp\Framework\Constant\Env;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    private const KEY = "FOOBAR001";

    public function setUp(): void
    {
        Environment::unsetVar(static::KEY);
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function tearDown(): void
    {
        Environment::unsetVar(static::KEY);
        Environment::unsetVar(Env::ENV_NAME);
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

    public function testOnlyIsStagingIsTrueIfEnvNameIsStaging(): void
    {
        Environment::setVar(Env::ENV_NAME, Environment::STAGING);
        $this->assertFalse(Environment::isTesting());
        $this->assertFalse(Environment::isDevelopment());
        $this->assertFalse(Environment::isProduction());
        $this->assertTrue(Environment::isStaging());
    }

    public function testOnlyIsTestingIsTrueIfEnvNameIsTesting(): void
    {
        Environment::setVar(Env::ENV_NAME, Environment::TESTING);
        $this->assertFalse(Environment::isStaging());
        $this->assertFalse(Environment::isDevelopment());
        $this->assertFalse(Environment::isProduction());
        $this->assertTrue(Environment::isTesting());
    }

    public function testOnlyIsDevelopmentIsTrueIfEnvNameIsDevelopment(): void
    {
        Environment::setVar(Env::ENV_NAME, Environment::DEVELOPMENT);
        $this->assertFalse(Environment::isStaging());
        $this->assertFalse(Environment::isTesting());
        $this->assertFalse(Environment::isProduction());
        $this->assertTrue(Environment::isDevelopment());
    }

    public function testOnlyIsProductionIsTrueIfEnvNameIsProduction(): void
    {
        Environment::setVar(Env::ENV_NAME, Environment::PRODUCTION);
        $this->assertFalse(Environment::isStaging());
        $this->assertFalse(Environment::isTesting());
        $this->assertFalse(Environment::isDevelopment());
        $this->assertTrue(Environment::isProduction());
    }
}
