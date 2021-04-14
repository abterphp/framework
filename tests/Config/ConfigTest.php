<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private const CATEGORY = "FOO";
    private const SETTING  = "BAR01";

    public function testMustGetGetsDefaultIfEnvironmentVariableIsMissing(): void
    {
        $value = 17;

        $this->assertEquals($value, Config::mustGet(static::CATEGORY, static::SETTING, $value));
    }

    public function testMustGetGetsEnvironmentVariableValueByDefault(): void
    {
        $value = 17;
        $wrong = "BARBAZ002";

        Config::set(static::CATEGORY, static::SETTING, $value);

        $this->assertEquals($value, Config::mustGet(static::CATEGORY, static::SETTING, $wrong));
    }

    public function testMustGetThrowsExceptionIfEnvironmentVariableAndDefaultAreMissing(): void
    {
        Config::set(static::CATEGORY, static::SETTING, null);

        $this->expectException(\RuntimeException::class);

        Config::mustGet(static::CATEGORY, static::SETTING);
    }

    public function testMustGetStringThrowsExceptionIfConfigIsInvalid(): void
    {
        Config::set(static::CATEGORY, static::SETTING, new \stdClass());

        $this->expectException(\RuntimeException::class);

        Config::mustGetString(static::CATEGORY, static::SETTING);
    }

    public function testMustGetStringReturnsStringIfConfigIsScalar(): void
    {
        Config::set(static::CATEGORY, static::SETTING, true);

        $actualResult = Config::mustGetString(static::CATEGORY, static::SETTING);

        $this->assertEquals('1', $actualResult);
    }

    /**
     * @return array
     */
    public function mustGetBoolDataProvider(): array
    {
        return [
            ['0', false],
            ['1', true],
            ['false', false],
            ['true', true],
            [0, false],
            [1, true],
        ];
    }

    /**
     * @dataProvider mustGetBoolDataProvider
     *
     * @param bool|int|float|string $value
     * @param bool                  $expectedResult
     */
    public function testMustGetBoolReturnsBoolIfConfigIsBooleanLikeString($value, bool $expectedResult): void
    {
        Config::set(static::CATEGORY, static::SETTING, $value);

        $actualResult = Config::mustGetBool(static::CATEGORY, static::SETTING);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function mustGetIntDataProvider(): array
    {
        return [
            ['354', 354],
            ['-17', -17],
        ];
    }

    /**
     * @dataProvider mustGetIntDataProvider
     *
     * @param bool|int|float|string $value
     * @param int                   $expectedResult
     */
    public function testMustGetIntReturnsBoolIfConfigIsBooleanLikeString($value, int $expectedResult): void
    {
        Config::set(static::CATEGORY, static::SETTING, $value);

        $actualResult = Config::mustGetInt(static::CATEGORY, static::SETTING);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function mustGetFloatDataProvider(): array
    {
        return [
            ['354.45', 354.45],
            ['-17.94', -17.94],
        ];
    }

    /**
     * @dataProvider mustGetFloatDataProvider
     *
     * @param bool|int|float|string $value
     * @param float                 $expectedResult
     */
    public function testMustGetFloatReturnsBoolIfConfigIsBooleanLikeString($value, float $expectedResult): void
    {
        Config::set(static::CATEGORY, static::SETTING, $value);

        $actualResult = Config::mustGetFloat(static::CATEGORY, static::SETTING);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
