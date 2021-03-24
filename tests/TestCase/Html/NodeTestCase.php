<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestCase\Html;

use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

abstract class NodeTestCase extends TestCase
{
    /**
     * @return array[]
     */
    public function setContentFailureProvider(): array
    {
        return [
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider setContentFailureProvider
     *
     * @param mixed $content
     */
    public function testCreateFailure($content): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createNode($content);
    }

    /**
     * @dataProvider setContentFailureProvider
     *
     * @param mixed $content
     */
    public function testSetContentFailure($content): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = $this->createNode();

        $sut->setContent($content);
    }

    public function testDefaultToString(): void
    {
        $sut = $this->createNode();

        $this->assertSame('', (string)$sut);
    }

    /**
     * @return array[]
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string' => ['foo', 'foo'],
        ];
    }

    /**
     * @dataProvider toStringReturnsRawContentByDefaultProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringReturnsRawContentByDefault($rawContent, string $expectedResult): void
    {
        $sut = $this->createNode($rawContent);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'string' => ['foo', $translations, 'bar'],
        ];
    }

    /**
     * @dataProvider toStringCanReturnTranslatedContentProvider
     *
     * @param mixed  $rawContent
     * @param array  $translations
     * @param string $expectedResult
     */
    public function testToStringCanReturnTranslatedContent(
        $rawContent,
        array $translations,
        string $expectedResult
    ): void {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($rawContent);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @return array
     */
    abstract public function isMatchProvider(): array;

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null $className
     * @param string[]    $intents
     * @param bool        $expectedResult
     */
    public function testIsMatch(?string $className, array $intents, bool $expectedResult): void
    {
        $sut = $this->createNode();
        $sut->setIntent('foo', 'bar');

        $actualResult = $sut->isMatch($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testAddIntent(): void
    {
        $intent0 = 'foo';
        $intent1 = 'bar';

        $sut = $this->createNode();

        $sut->addIntent($intent0);
        $sut->addIntent($intent1);

        $intents = $sut->getIntents();

        $this->assertSame([$intent0, $intent1], $intents);
    }

    public function testHasIntent(): void
    {
        $intent0 = 'foo';
        $intent1 = 'bar';

        $sut = $this->createNode();

        $sut->addIntent($intent0);
        $sut->addIntent($intent1);

        $this->assertTrue($sut->hasIntent($intent0));
    }

    /**
     * @param INode|string|null $content
     *
     * @return Node
     */
    private function createNode($content = null): INode
    {
        return new Node($content);
    }
}
