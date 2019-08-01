<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\ComponentTest;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\I18n\MockTranslatorFactory;

class ItemTest extends ComponentTest
{
    public function testDefaultToString()
    {
        $sut = $this->createNode();

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
    public function testToStringWithTranslation($content, array $translations, string $expectedResult)
    {
        $translator = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($content);
        $sut->setTranslator($translator);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider toStringReturnsRawContentByDefaultProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringReturnsRawContentByDefault($rawContent, string $expectedResult)
    {
        $sut = $this->createNode($rawContent);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider toStringCanReturnTranslatedContentProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringCanReturnTranslatedContent($rawContent, array $translations, string $expectedResult)
    {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($rawContent);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    public function testGetResourceGetsLastSetResource()
    {
        $content  = 'foo';
        $resource = 'bar';

        $sut = $this->createNode($content);

        $sut->setResource($resource);

        $actualResult = $sut->getResource();

        $this->assertSame($resource, $actualResult);
    }

    public function testGetRoleGetsLastSetResource()
    {
        $content  = 'foo';
        $role = 'bar';

        $sut = $this->createNode($content);

        $sut->setRole($role);

        $actualResult = $sut->getRole();

        $this->assertSame($role, $actualResult);
    }

    public function testDisabledItemCastsToEmptyString()
    {
        $content  = 'foo';
        $sut = $this->createNode($content);

        $sut->disable();

        $this->assertSame('', (string)$sut);
    }

    public function testEnableCanRevertDisabling()
    {
        $content  = 'foo';
        $sut = $this->createNode($content);

        $sut->disable();
        $sut->enable();

        $this->assertNotSame('', (string)$sut);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Item
     */
    protected function createNode($content = null): INode
    {
        return new Item($content);
    }
}
