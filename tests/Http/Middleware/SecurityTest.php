<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use Opulence\Cache\ArrayBridge;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    /** @var Security - System Under Test */
    protected $sut;

    /** @var ArrayBridge|MockObject */
    protected $cacheBridgeMock;

    public function setUp()
    {
        $this->cacheBridgeMock = $this->getMockBuilder(ArrayBridge::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set'])
            ->getMock();

        $this->sut = new Security($this->cacheBridgeMock);
    }

    public function testHandleSkipsAllIfEnvironmentIsNotProduction()
    {
        $this->sut->setEnvironment(Environment::TESTING);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $this->cacheBridgeMock->expects($this->never())->method('get');

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleSkipsAllIfCacheIsSet()
    {
        $this->sut->setEnvironment(Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $this->cacheBridgeMock->expects($this->once())->method('get')->willReturn(true);

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleSetsCacheIfAllGood()
    {
        $this->sut->setEnvironment(Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $this->sut->setEnvironmentData(
            [
                Env::DB_PASSWORD              => 'abc',
                Env::ENCRYPTION_KEY           => 'bcd',
                Env::CRYPTO_FRONTEND_SALT     => 'cde',
                Env::CRYPTO_ENCRYPTION_PEPPER => 'def',
            ]
        );

        $this->sut->setSettings(['display_errors' => '']);

        $this->cacheBridgeMock->expects($this->any())->method('get')->willReturn(false);
        $this->cacheBridgeMock->expects($this->once())->method('set')->willReturn(true);

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleRunsNormalIfCacheGetThrowsException()
    {
        $this->sut->setEnvironment(Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $this->sut->setEnvironmentData(
            [
                Env::DB_PASSWORD              => 'abc',
                Env::ENCRYPTION_KEY           => 'bcd',
                Env::CRYPTO_FRONTEND_SALT     => 'cde',
                Env::CRYPTO_ENCRYPTION_PEPPER => 'def',
            ]
        );

        $this->sut->setSettings(['display_errors' => '']);

        $this->cacheBridgeMock->expects($this->any())->method('get')->willThrowException(new \Exception());
        $this->cacheBridgeMock->expects($this->once())->method('set')->willReturn(true);

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function handleThrowsSecurityExceptionIfNeededProvider(): array
    {
        return [
            'test-password'          => [
                [
                    Env::DB_PASSWORD              => Security::TEST_DB_PASSWORD,
                    Env::ENCRYPTION_KEY           => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT     => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER => 'def',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-encryption-key'    => [
                [
                    Env::DB_PASSWORD              => 'abc',
                    Env::ENCRYPTION_KEY           => Security::TEST_ENCRYPTION_KEY,
                    Env::CRYPTO_FRONTEND_SALT     => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER => 'def',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-frontend-salt'     => [
                [
                    Env::DB_PASSWORD              => 'abc',
                    Env::ENCRYPTION_KEY           => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT     => Security::TEST_CRYPTO_FRONTEND_SALT,
                    Env::CRYPTO_ENCRYPTION_PEPPER => 'def',
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'test-encryption-pepper' => [
                [
                    Env::DB_PASSWORD              => 'abc',
                    Env::ENCRYPTION_KEY           => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT     => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER => Security::TEST_CRYPTO_ENCRYPTION_PEPPER,
                ],
                [
                    'display_errors' => '',
                ],
            ],
            'display_errors-on' => [
                [
                    Env::DB_PASSWORD              => 'abc',
                    Env::ENCRYPTION_KEY           => 'bcd',
                    Env::CRYPTO_FRONTEND_SALT     => 'cde',
                    Env::CRYPTO_ENCRYPTION_PEPPER => 'def',
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
     * @expectedException \AbterPhp\Framework\Security\SecurityException
     *
     * @param array $environmentData
     * @param array $settingsData
     */
    public function testHandleThrowsSecurityExceptionIfNeeded(array $environmentData, array $settingsData)
    {
        $this->sut->setEnvironment(Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $this->sut->setEnvironmentData($environmentData);

        $this->sut->setSettings($settingsData);

        $this->cacheBridgeMock->expects($this->any())->method('get')->willReturn(false);

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }
}
