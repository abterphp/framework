<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EnvironmentWarningTest extends TestCase
{
    /** @var ITranslator|MockObject */
    protected $translatorMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->translatorMock = MockTranslatorFactory::createSimpleTranslator($this, []);
    }

    public function testHandleInProduction(): void
    {
        $sut = new EnvironmentWarning($this->translatorMock, Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $responseMock->expects($this->never())->method('setContent');

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleInTesting(): void
    {
        $sut = new EnvironmentWarning($this->translatorMock, Environment::TESTING);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->createMock(Response::class);

        $next = fn () => $responseMock;

        $responseMock->expects($this->atLeastOnce())->method('setContent');

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }
}
