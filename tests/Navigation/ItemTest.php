<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testDefaultToString(): void
    {
        $sut = $this->createItem();

        $this->assertSame('<li></li>', (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringWithTranslationProvider(): array
    {
        return [
            ['AAA', ['AAA' => 'BBB'], '<li>BBB</li>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string'  => ['foo', '<li>foo</li>'],
            'INode'   => [new Node('foo'), '<li>foo</li>'],
            'INode[]' => [[new Node('foo')], '<li>foo</li>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'string'  => ['foo', $translations, '<li>bar</li>'],
            'INode'   => [new Node('foo'), $translations, '<li>bar</li>'],
            'INode[]' => [[new Node('foo')], $translations, '<li>bar</li>'],
        ];
    }

    /**
     * @dataProvider toStringWithTranslationProvider
     *
     * @param        $content
     * @param array  $translations
     * @param string $expectedResult
     */
    public function testToStringWithTranslation($content, array $translations, string $expectedResult): void
    {
        $translator = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createItem($content);
        $sut->setTranslator($translator);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider toStringReturnsRawContentByDefaultProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringReturnsRawContentByDefault($rawContent, string $expectedResult): void
    {
        $sut = $this->createItem($rawContent);

        $this->assertStringContainsString($expectedResult, (string)$sut);
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

        $sut = $this->createItem($rawContent);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    public function testGetResourceGetsLastSetResource(): void
    {
        $content  = 'foo';
        $resource = 'bar';

        $sut = $this->createItem($content);

        $sut->setResource($resource);

        $actualResult = $sut->getResource();

        $this->assertSame($resource, $actualResult);
    }

    public function testGetRoleGetsLastSetResource(): void
    {
        $content = 'foo';
        $role    = 'bar';

        $sut = $this->createItem($content);

        $sut->setRole($role);

        $actualResult = $sut->getRole();

        $this->assertSame($role, $actualResult);
    }

    public function testDisabledItemCastsToEmptyString(): void
    {
        $content = 'foo';
        $sut     = $this->createItem($content);

        $sut->disable();

        $this->assertSame('', (string)$sut);
    }

    public function testEnableCanRevertDisabling(): void
    {
        $content = 'foo';
        $sut     = $this->createItem($content);

        $sut->disable();
        $sut->enable();

        $this->assertNotSame('', (string)$sut);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Item
     */
    protected function createItem($content = null): Item
    {
        return new Item($content);
    }
}
