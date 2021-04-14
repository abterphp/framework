<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\ComponentTest;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;

class SortableTest extends ComponentTest
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attribs = StubAttributeFactory::createAttributes();

        return [
            'simple'               => ['ABC', 'a', '', '', null, null, null, "<th>ABC <a></a></th>"],
            'with attributes'      => ['ABC', 'a', '', '', $attribs, null, null, "<th$attribs>ABC <a></a></th>"],
            'missing translations' => ['ABC', 'a', '', '', null, [], null, "<th>ABC <a></a></th>"],
            'custom tag'           => ['ABC', 'a', '', '', null, null, 'myth', "<myth>ABC <a></a></myth>"],
            'with translations'    => ['ABC', 'a', '', '', null, ['ABC' => 'CBA'], null, "<th>CBA <a></a></th>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string          $content
     * @param string          $group
     * @param string          $inputName
     * @param string          $fieldName
     * @param Attributes|null $attributes
     * @param array|null      $translations
     * @param string|null     $tag
     * @param string          $expectedResult
     */
    public function testRender(
        string $content,
        string $group,
        string $inputName,
        string $fieldName,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createNode($content, $group, $inputName, $fieldName, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    /**
     * @return array[]
     */
    public function queryParamProvider(): array
    {
        return [
            'empty'    => [[], null],
            'not-set'  => [['x' => 10], null],
            'zero'     => [['sort-i' => 0], null],
            'non-zero' => [['sort-i' => -1], 'sort-i=-1&'],
        ];
    }

    /**
     * @dataProvider queryParamProvider
     *
     * @param array       $params
     * @param string|null $expectedResult
     */
    public function testQueryParam(array $params, ?string $expectedResult): void
    {
        $sut = new Sortable(null, 'g', 'i', 'f');

        $sut->setParams($params);

        $actualResult = $sut->getQueryParam();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function shoartingProvider(): array
    {
        $intentClassMap = [
            Sortable::BTN_INTENT_CARET_ACTIVE => 'ica',
            Sortable::BTN_INTENT_CARET_DOWN   => 'icd',
            Sortable::BTN_INTENT_CARET_UP     => 'icu',
            Sortable::BTN_INTENT_SHOARTING    => 's',
        ];

        return [
            'empty'    => [[], $intentClassMap, '<th>ABC <a href="sort-i=1" class="icd s"></a></th>'],
            'not-set'  => [['x' => 10], $intentClassMap, '<th>ABC <a href="sort-i=1" class="icd s"></a></th>'],
            'zero'     => [['sort-i' => 0], $intentClassMap, '<th>ABC <a href="sort-i=1" class="icd s"></a></th>'],
            'negative' => [
                ['sort-i' => -10],
                $intentClassMap,
                '<th>ABC <a href="sort-i=0" class="ica icu s"></a></th>',
            ],
            'positive' => [
                ['sort-i' => 10],
                $intentClassMap,
                '<th>ABC <a href="sort-i=-1" class="ica icd s"></a></th>',
            ],
        ];
    }

    /**
     * @dataProvider shoartingProvider
     *
     * @param array  $params
     * @param array  $intentClassMap
     * @param string $expectedResult
     */
    public function testShoarting(array $params, array $intentClassMap, string $expectedResult): void
    {
        $sut = new Sortable('ABC', 'g', 'i', 'f');

        $sut->setParams($params);

        $sortBtn = $sut->getSortBtn();
        $intents = $sortBtn->getIntents();
        foreach ($intentClassMap as $intent => $class) {
            if (in_array($intent, $intents, true)) {
                $sortBtn->appendToClass($class);
            }
        }

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    public function testGetExtendedNodesReturnsSortBtn(): void
    {
        $sut = new Sortable('ABC', 'g', 'i', 'f');

        $allNodes = $sut->getExtendedNodes();

        $this->assertCount(2, $allNodes);
        $this->assertContains($sut->getSortBtn(), $allNodes);
    }

    public function testBaseUrlIsInSortBtnHref(): void
    {
        $baseUrl = '/foobar';
        $params  = ['sort-i' => 10];

        $sut = new Sortable('ABC', 'g', 'i', 'f');
        $sut->setBaseUrl($baseUrl);
        $sut->setParams($params);

        $this->assertStringContainsString($baseUrl, (string)$sut);
    }

    /**
     * @return array[]
     */
    public function getQueryPartProvider(): array
    {
        return [
            'empty'    => [[], ''],
            'not-set'  => [['x' => 10], ''],
            'zero'     => [['sort-i' => 0], ''],
            'non-zero' => [['sort-i' => -1], 'sort-i=-1'],
        ];
    }

    /**
     * @dataProvider getQueryPartProvider
     *
     * @param array  $params
     * @param string $expectedResult
     */
    public function testGetQueryPart(array $params, string $expectedResult): void
    {
        $sut = new Sortable('ABC', 'g', 'i', 'f');
        $sut->setParams($params);

        $actualResult = $sut->getQueryPart();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function getQuerySortConditionsProvider(): array
    {
        return [
            'empty'         => [[], []],
            'not-set'       => [['x' => 10], []],
            'zero'          => [['sort-i' => 0], []],
            'non-zero-desc' => [['sort-i' => -1], ['f DESC']],
            'non-zero-asc'  => [['sort-i' => 1], ['f ASC']],
        ];
    }

    /**
     * @dataProvider getQuerySortConditionsProvider
     *
     * @param array $params
     * @param array $expectedResult
     */
    public function testGetSortConditions(array $params, array $expectedResult): void
    {
        $sut = new Sortable('ABC', 'g', 'i', 'f');
        $sut->setParams($params);

        $actualResult = $sut->getSortConditions();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetTemplate(): void
    {
        $expectedResult = '<th>ABC <a></a> --||-- <a></a> ABC</th>';

        $template = '%1$s %2$s --||-- %2$s %1$s';

        $sut = new Sortable('ABC', 'g', 'i', 'f');
        $sut->setTemplate($template);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    /**
     * @return array[]
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string' => ['foo', '<th>foo <a></a></th>'],
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
        $sut = $this->createNode($rawContent, 'g', 'i', 'f', null, null, null);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @return array[]
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
     * @param string|null $rawContent
     * @param array       $translations
     * @param string      $expectedResult
     */
    public function testToStringCanReturnTranslatedContent(
        $rawContent,
        array $translations,
        string $expectedResult
    ): void {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($rawContent, 'g', 'i', 'f', null, null, null);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null $className
     * @param string[]    $intents
     * @param bool        $expectedResult
     */
    public function testIsMatch(?string $className, array $intents, bool $expectedResult): void
    {
        $sut = $this->createNode(null, 'g', 'i', 'f', null, null, null);
        $sut->setIntent('foo', 'bar');

        $actualResult = $sut->isMatch($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string|null     $content
     * @param string          $group
     * @param string          $inputName
     * @param string          $fieldName
     * @param Attributes|null $attributes
     * @param array|null      $translations
     * @param string|null     $tag
     *
     * @return Sortable
     */
    private function createNode(
        ?string $content,
        string $group,
        string $inputName,
        string $fieldName,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag
    ): Sortable {
        $cell = new Sortable($content, $group, $inputName, $fieldName, [], $attributes, $tag);

        $cell->setTranslator(MockTranslatorFactory::createSimpleTranslator($this, $translations));

        return $cell;
    }
}
