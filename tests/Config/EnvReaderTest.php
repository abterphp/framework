<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;
use PHPUnit\Framework\TestCase;

class EnvReaderTest extends TestCase
{
    /** @var EnvReader - System Under Test */
    protected EnvReader $sut;

    public function setUp(): void
    {
        $this->sut = new EnvReader();

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->sut->set(Env::ENV_NAME, Environment::TESTING);
    }

    /**
     * @return array[]
     */
    public function isStagingProvider(): array
    {
        return [
            'staging'     => ['staging', true],
            'testing'     => ['testing', false],
            'development' => ['development', false],
            'production'  => ['production', false],
            'foo'         => ['foo', false],
        ];
    }

    /**
     * @dataProvider isStagingProvider
     *
     * @param string $envName
     * @param bool   $expectedResult
     */
    public function testIsStaging(string $envName, bool $expectedResult): void
    {
        $this->sut->set(Env::ENV_NAME, $envName);

        $actualResult = $this->sut->isStaging();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function isTestingProvider(): array
    {
        return [
            'staging'     => ['staging', false],
            'testing'     => ['testing', true],
            'development' => ['development', false],
            'production'  => ['production', false],
            'foo'         => ['foo', false],
        ];
    }

    /**
     * @dataProvider isTestingProvider
     *
     * @param string $envName
     * @param bool   $expectedResult
     */
    public function testIsTesting(string $envName, bool $expectedResult): void
    {
        $this->sut->set(Env::ENV_NAME, $envName);

        $actualResult = $this->sut->isTesting();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function isDevelopmentProvider(): array
    {
        return [
            'staging'     => ['staging', false],
            'testing'     => ['testing', false],
            'development' => ['development', true],
            'production'  => ['production', false],
            'foo'         => ['foo', false],
        ];
    }

    /**
     * @dataProvider isDevelopmentProvider
     *
     * @param string $envName
     * @param bool   $expectedResult
     */
    public function testIsDevelopment(string $envName, bool $expectedResult): void
    {
        $this->sut->set(Env::ENV_NAME, $envName);

        $actualResult = $this->sut->isDevelopment();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function isProductionProvider(): array
    {
        return [
            'staging'     => ['staging', false],
            'testing'     => ['testing', false],
            'development' => ['development', false],
            'production'  => ['production', true],
            'foo'         => ['foo', false],
        ];
    }

    /**
     * @dataProvider isProductionProvider
     *
     * @param string $envName
     * @param bool   $expectedResult
     */
    public function testIsProduction(string $envName, bool $expectedResult): void
    {
        $this->sut->set(Env::ENV_NAME, $envName);

        $actualResult = $this->sut->isProduction();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIsReturnsFalseIfEnvironmentVariableIsNotSet(): void
    {
        $name  = 'foo';
        $value = 'bar';

        $actualResult = $this->sut->is($name, $value);

        $this->assertFalse($actualResult);
    }

    public function testIsReturnsFalseIfEnvironmentVariableIsNotSetEvenComparedAgainstEmpty(): void
    {
        $name  = 'foo';
        $value = '';

        $actualResult = $this->sut->is($name, $value);

        $this->assertFalse($actualResult);
    }

    public function testIsReturnsTrueIfEnvironmentVariableIsNotSetButDefaultIsSame(): void
    {
        $name  = 'foo';
        $value = 'bar';

        $actualResult = $this->sut->is($name, $value);

        $this->assertFalse($actualResult);
    }

    public function testIsReturnsTrueIfExpectedValueIsSetAsEnvironmentVariable(): void
    {
        $name  = 'foo';
        $value = 'bar';

        $this->sut->set($name, $value);

        $actualResult = $this->sut->is($name, $value);

        $this->assertTrue($actualResult);

        $this->sut->clear($name);
    }

    public function testIsIgnoresDefaultValueIfEnvironmentVariableIsSet(): void
    {
        $name   = 'foo';
        $value  = 'bar';
        $value2 = 'baz';

        $this->sut->set($name, $value);

        $actualResult = $this->sut->is($name, $value, $value2);

        $this->assertTrue($actualResult);

        $this->sut->clear($name);
    }

    public function testGetReturnsEmptyStringIfEnvironmentVariableIsNotSet(): void
    {
        $name = 'foo';

        $actualResult = $this->sut->get($name);

        $this->assertNull($actualResult);
    }

    public function testGetReturnsDefaultIfEnvironmentVariableIsNotSetButDefaultIs(): void
    {
        $name           = 'foo';
        $expectedResult = 'bar';

        $actualResult = $this->sut->get($name, $expectedResult);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetReturnsSetEnvironmentVariable(): void
    {
        $name  = 'foo';
        $value = 'bar';

        $this->sut->set($name, $value);

        $actualResult = $this->sut->get($name);

        $this->assertSame($value, $actualResult);

        $this->sut->clear($name);
    }

    public function testGetIgnoresDefaultValueIfEnvironmentVariableIsSet(): void
    {
        $name   = 'foo';
        $value  = 'bar';
        $value2 = 'baz';

        $this->sut->set($name, $value);

        $actualResult = $this->sut->get($name, $value2);

        $this->assertSame($value, $actualResult);

        $this->sut->clear($name);
    }
}
