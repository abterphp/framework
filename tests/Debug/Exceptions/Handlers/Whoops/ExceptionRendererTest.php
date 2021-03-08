<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Debug\Exceptions\Handlers\Whoops;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Whoops\RunInterface;

class ExceptionRendererTest extends TestCase
{
    public function testGetRunGetsRun()
    {
        $runMock = $this->createRunMock();

        $sut = new ExceptionRenderer($runMock, true);

        $actualResult = $sut->getRun();

        $this->assertSame($runMock, $actualResult);
    }

    public function testRenderCallsHandleException()
    {
        $isDevelopmentEnvironment = true;

        $exceptionStub = new \Exception();

        $runMock = $this->createRunMock();

        $runMock->expects($this->atLeastOnce())->method('handleException')->with($exceptionStub);

        $sut = new ExceptionRenderer($runMock, $isDevelopmentEnvironment);

        $sut->render($exceptionStub);
    }

    public function testRenderInProduction()
    {
        $isDevelopmentEnvironment = false;

        $exceptionStub = new \Exception();

        $runMock = $this->createRunMock();

        $runMock->expects($this->never())->method('handleException');

        $sut = new ExceptionRenderer($runMock, $isDevelopmentEnvironment);

        $sut->render($exceptionStub);
    }

    /**
     * @return MockObject|RunInterface
     */
    protected function createRunMock()
    {
        $mock = $this->createMock(RunInterface::class);

        return $mock;
    }
}
