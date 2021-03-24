<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Session;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    /** @var FlashService - System Under Test */
    protected FlashService $sut;

    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var ITranslator|MockObject */
    protected $translatorMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->sessionMock = $this->createMock(Session::class);

        $this->translatorMock = MockTranslatorFactory::createSimpleTranslator($this, []);

        $this->sut = new FlashService($this->sessionMock, $this->translatorMock);
    }

    /**
     * @return array[]
     */
    public function mergeSuccessMessagesProvider(): array
    {
        return [
            [
                ['AAA' => 'BBB'],
                ['BBB' => 'CCC'],
                ['AAA' => 'BBB', 'BBB' => 'CCC'],
            ],
        ];
    }

    /**
     * @dataProvider mergeSuccessMessagesProvider
     *
     * @param array $oldMessages
     * @param array $newMessages
     * @param array $expectedResult
     */
    public function testMergeSuccessMessages(array $oldMessages, array $newMessages, array $expectedResult): void
    {
        $this->sessionMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($oldMessages);

        $this->sessionMock
            ->expects($this->atLeastOnce())
            ->method('flash')
            ->with('success', $expectedResult);

        $this->sut->mergeSuccessMessages($newMessages);
    }

    /**
     * @return array
     */
    public function mergeErrorMessagesProvider(): array
    {
        return [
            [
                ['AAA', 'BBB'],
                ['BBB' => ['CCC']],
                ['AAA', 'BBB', 'CCC'],
            ],
        ];
    }

    /**
     * @dataProvider mergeErrorMessagesProvider
     *
     * @param array $oldMessages
     * @param array $newMessages
     * @param array $expectedResult
     */
    public function testMergeErrorMessages(array $oldMessages, array $newMessages, array $expectedResult): void
    {
        $this->sessionMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($oldMessages);

        $this->sessionMock
            ->expects($this->atLeastOnce())
            ->method('flash')
            ->with('error', $expectedResult);

        $this->sut->mergeErrorMessages($newMessages);
    }

    public function testRetrieveSuccessMessages(): void
    {
        $expectedResult = ['bar'];

        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('success')
            ->willReturn($expectedResult);

        $actualResult = $this->sut->retrieveSuccessMessages();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testRetrieveErrorMessages(): void
    {
        $expectedResult = ['bar'];

        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('error')
            ->willReturn($expectedResult);

        $actualResult = $this->sut->retrieveErrorMessages();

        $this->assertSame($expectedResult, $actualResult);
    }
}
