<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use Opulence\Framework\Configuration\Config;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SessionHandlerInterface;

class SessionTest extends TestCase
{
    /** @var Session - System Under Test */
    protected Session $sut;

    protected string $sessionId = 'foo-session-id';

    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var SessionHandlerInterface|MockObject */
    protected $sessionHandlerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->sessionMock = $this->createMock(ISession::class);
        $this->sessionMock->expects($this->any())->method('getId')->willReturn($this->sessionId);

        $this->sessionHandlerMock = $this->getMockBuilder(SessionHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new Session($this->sessionMock, $this->sessionHandlerMock);
    }

    public function testHandleWithoutGc(): void
    {
        Config::set('sessions', 'gc.divisor', 1);
        Config::set('sessions', 'gc.chance', 0);

        Config::set('sessions', 'lifetime', 0);
        Config::set('sessions', 'cookie.path', '');
        Config::set('sessions', 'cookie.domain', '');
        Config::set('sessions', 'cookie.isSecure', false);
        Config::set('sessions', 'cookie.isHttpOnly', false);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleWithGc(): void
    {
        Config::set('sessions', 'gc.divisor', 1);
        Config::set('sessions', 'gc.chance', 2);

        Config::set('sessions', 'lifetime', 0);
        Config::set('sessions', 'cookie.path', '');
        Config::set('sessions', 'cookie.domain', '');
        Config::set('sessions', 'cookie.isSecure', false);
        Config::set('sessions', 'cookie.isHttpOnly', false);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $this->sessionHandlerMock->expects($this->once())->method('gc');

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }
}
