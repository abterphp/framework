<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use Exception;
use Opulence\Cache\ArrayBridge;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    /** @var ArrayBridge|MockObject */
    protected $cacheBridgeMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheBridgeMock = $this->createMock(ArrayBridge::class);
    }

    public function testHandleSkipsAllIfEnvironmentIsNotProduction(): void
    {
        $sut = new Security($this->cacheBridgeMock, Environment::TESTING);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $this->cacheBridgeMock->expects($this->never())->method('get');

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleSkipsAllIfCacheIsSet(): void
    {
        $sut = new Security($this->cacheBridgeMock, Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $this->cacheBridgeMock->expects($this->once())->method('get')->willReturn(true);

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleSetsCacheIfAllGood(): void
    {
        $sut = new Security($this->cacheBridgeMock, Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $sut->setVar(
            [
                Env::DB_PASSWORD                 => 'abc',
                Env::ENCRYPTION_KEY              => 'bcd',
                Env::CRYPTO_FRONTEND_SALT        => 'cde',
                Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
            ]
        );

        $sut->setSettings(['display_errors' => '']);

        $this->cacheBridgeMock->expects($this->any())->method('get')->willReturn(false);
        $this->cacheBridgeMock->expects($this->once())->method('set')->willReturn(true);

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleRunsNormalIfCacheGetThrowsException(): void
    {
        $sut = new Security($this->cacheBridgeMock, Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $sut->setVar(
            [
                Env::DB_PASSWORD                 => 'abc',
                Env::ENCRYPTION_KEY              => 'bcd',
                Env::CRYPTO_FRONTEND_SALT        => 'cde',
                Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
            ]
        );

        $sut->setSettings(['display_errors' => '']);

        $this->cacheBridgeMock->expects($this->any())->method('get')->willThrowException(new Exception());
        $this->cacheBridgeMock->expects($this->once())->method('set')->willReturn(true);

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function handleThrowsSecurityExceptionIfNeededProvider(): array
    {
        return [
            'test-password'                    => [
                [
                    Env::DB_PASSWORD                 => Security::TEST_DB_PASSWORD,
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-encryption-key'              => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => Security::TEST_ENCRYPTION_KEY,
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-frontend-salt'               => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => Security::TEST_CRYPTO_FRONTEND_SALT,
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-encryption-pepper'           => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => Security::TEST_CRYPTO_ENCRYPTION_PEPPER,
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-oauth2-private-key-path'     => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => Security::TEST_OAUTH2_PRIVATE_KEY_PATH,
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-oauth2-private-key-password' => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => Security::TEST_OAUTH2_PRIVATE_KEY_PASSWORD,
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-oauth2-public-key-path'      => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => Security::TEST_OAUTH2_PUBLIC_KEY_PATH,
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-oauth2-encryption-key'       => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => Security::TEST_OAUTH2_ENCRYPTION_KEY,
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'display_errors-on'                => [
                [
                    Env::DB_PASSWORD                 => 'abc',
                    Env::ENCRYPTION_KEY              => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT        => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER    => 'def',
                    Env::OAUTH2_PRIVATE_KEY_PATH     => 'efg',
                    Env::OAUTH2_PRIVATE_KEY_PASSWORD => 'fgh',
                    Env::OAUTH2_PUBLIC_KEY_PATH      => 'ghi',
                    Env::OAUTH2_ENCRYPTION_KEY       => 'hij',
                ],
                [
                    'display_errors' => '1',
                ],
            ],
        ];
    }

    /**
     * @dataProvider handleThrowsSecurityExceptionIfNeededProvider
     *
     * @param array $environmentData
     * @param array $settingsData
     */
    public function testHandleThrowsSecurityExceptionIfNeeded(array $environmentData, array $settingsData): void
    {
        $this->expectException(\AbterPhp\Framework\Exception\Security::class);

        $sut = new Security($this->cacheBridgeMock, Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $sut->setVar($environmentData);

        $sut->setSettings($settingsData);

        $this->cacheBridgeMock->expects($this->any())->method('get')->willReturn(false);

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testGetSettingsCanUseValuesSet(): void
    {
        $key   = 'foo';
        $value = 'bar';

        $sut = new Security($this->cacheBridgeMock, Environment::PRODUCTION);

        $sut->setSettings([$key => $value]);

        $actualValue = $sut->getSetting($key);

        $this->assertSame($actualValue, $value);
    }

    public function testGetSettingsCanCheckPhpSettingsIfValueIsNotSet(): void
    {
        $key   = 'memory_limit';
        $value = ini_get($key);

        $sut = new Security($this->cacheBridgeMock, Environment::PRODUCTION);

        $actualValue = $sut->getSetting($key);

        $this->assertSame($actualValue, $value);
    }
}
