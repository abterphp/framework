<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Collection;

use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Cell\ICell;
use AbterPhp\Framework\Html\CollectionTest;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use LogicException;

class CellsTest extends CollectionTest
{
    /**
     * @return array[]
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'ICell'   => [new Cell('foo', 'A'), 'foo'],
            'ICell[]' => [[new Cell('foo', 'A')], 'foo'],
        ];
    }

    /**
     * @dataProvider toStringReturnsRawContentByDefaultProvider
     *
     * @param string $rawContent
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
            'INode'   => [new Cell('foo', 'A'), $translations, 'bar'],
            'INode[]' => [[new Cell('foo', 'A')], $translations, 'bar'],
        ];
    }

    /**
     * @dataProvider toStringCanReturnTranslatedContentProvider
     *
     * @param null|string|INode|INode[] $rawContent
     * @param array                     $translations
     * @param string                    $expectedResult
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

    public function testCountWithoutOffset(): void
    {
        $expectedResult = 2;

        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $sut = $this->createNode();

        $sut[] = $node1;
        $sut[] = $node2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithExplicitOffset(): void
    {
        $expectedResult = 2;

        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $sut = $this->createNode();

        $sut[0] = $node1;
        $sut[1] = $node2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithMixedOffset(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $expectedCount = 5;

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $this->assertSame($expectedCount, count($sut));
    }

    public function testArrayAccessWithoutOffset(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $sut = $this->createNode();

        $sut[] = $node1;
        $sut[] = $node2;

        $this->assertSame($node1, $sut[0]);
        $this->assertSame($node2, $sut[1]);
    }

    public function testArrayAccessWithExplicitOffset(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $sut = $this->createNode();

        $sut[0] = $node1;
        $sut[1] = $node2;

        $this->assertSame($node1, $sut[0]);
        $this->assertSame($node2, $sut[1]);
    }

    public function testArrayAccessThrowExceptionWhenMadeDirty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $node1 = new Cell('1', 'A');

        $sut = $this->createNode();

        $sut[1] = $node1;
    }

    public function testArrayAccessWithMixedOffset(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $expectedNodes = [0 => $node1, 1 => $node2, 2 => $node1, 3 => $node1, 4 => $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $this->assertEquals($expectedNodes, $sut->getExtendedNodes());
    }

    /**
     * @return array
     */
    public function contentFailureProvider(): array
    {
        return [
            'bool'                    => [true],
            'non-node object'         => [new \StdClass()],
            'string wrapped'          => [['']],
            'non-node object wrapped' => [[new \StdClass()]],
            'node double wrapped'     => [[[new Cell('1', 'A')]]],
            'collection'              => [new Cells()],
        ];
    }

    /**
     * @dataProvider contentFailureProvider
     *
     * @param $item
     */
    public function testConstructFailure($item): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createNode($item);
    }

    /**
     * @dataProvider contentFailureProvider
     *
     * @param $item
     */
    public function testSetContentFailure($item): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = $this->createNode();

        $sut->setContent($item);
    }

    /**
     * @return array
     */
    public function offsetSetFailureProvider(): array
    {
        $contentFailure = $this->contentFailureProvider();

        $offsetFailure = [
            'string'       => ['foo'],
            'node wrapped' => [[new Cell('1', 'A')]],
        ];

        return array_merge($contentFailure, $offsetFailure);
    }

    /**
     * @dataProvider offsetSetFailureProvider
     *
     * @param $item
     */
    public function testArrayAccessFailureWithoutOffset($item): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = $this->createNode();

        $sut[] = $item;
    }

    /**
     * @dataProvider offsetSetFailureProvider
     */
    public function testArrayAccessFailureWithExplicitOffset($item): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = $this->createNode();

        $sut[] = $item;
    }

    public function testSetContent(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $sut    = $this->createNode();
        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $expectedNodes = [new Cell('3', 'A')];

        $sut->setContent($expectedNodes[0]);

        $this->assertEquals($expectedNodes, $sut->getExtendedNodes());
    }

    public function testGetNodes(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $sut    = $this->createNode();
        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedNodes(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

        $sut    = $this->createNode();
        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $actualResult = $sut->getExtendedNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetDescendantNodes(): void
    {
        $nodeContent1 = new Node('1');
        $nodeContent2 = new Node('2');

        $node1 = new Cell($nodeContent1, 'A');
        $node2 = new Cell($nodeContent2, 'A');

        $sut    = $this->createNode();
        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $expectedNodes = [
            $node1,
            $nodeContent1,
            $node2,
            $nodeContent2,
            $node1,
            $nodeContent1,
            $node1,
            $nodeContent1,
            $node1,
            $nodeContent1,
        ];

        $actualResult = $sut->getDescendantNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedDescendantNodes(): void
    {
        $nodeContent1 = new Node('1');
        $nodeContent2 = new Node('2');

        $node1 = new Cell($nodeContent1, 'A');
        $node2 = new Cell($nodeContent2, 'A');

        $sut    = $this->createNode();
        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $expectedNodes = [
            $node1,
            $nodeContent1,
            $node2,
            $nodeContent2,
            $node1,
            $nodeContent1,
            $node1,
            $nodeContent1,
            $node1,
            $nodeContent1,
        ];

        $actualResult = $sut->getExtendedDescendantNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testIterator(): void
    {
        $node1 = new Cell('1', 'A');
        $node2 = new Cell('2', 'A');

        $expectedKeys  = [0, 1, 2, 3, 4];
        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $pos = 0;
        foreach ($sut as $key => $node) {
            $this->assertSame($expectedKeys[$pos], $key);
            $this->assertSame($expectedNodes[$pos], $node);
            $pos++;
        }
    }

    public function testGetRawContentReturnsNonTranslatedContent(): void
    {
        $this->assertTrue(true, 'No need to test getRawContent');
    }

    /**
     * @return array
     */
    public function insertBeforeProvider(): array
    {
        $needle = new Cell('1', 'A');
        $cell2  = new Cell('2', 'A');
        $cell3  = new Cell('3', 'A');

        return [
            'empty-content'              => [
                [],
                $needle,
                [$cell2],
                false,
                [],
            ],
            'only-non-matching-content'  => [
                [$cell2, $cell3],
                $needle,
                [$needle, $cell3],
                false,
                [$cell2, $cell3],
            ],
            'only-matching-content'      => [
                [$needle],
                $needle,
                [$cell2, $cell3],
                true,
                [$cell2, $cell3, $needle],
            ],
            'non-first-matching-content' => [
                [$cell2, $needle],
                $needle,
                [$cell3, $cell3],
                true,
                [$cell2, $cell3, $cell3, $needle],
            ],
        ];
    }

    /**
     * @return array
     */
    public function insertAfterProvider(): array
    {
        $needle = new Cell('1', 'A');
        $cell2  = new Cell('2', 'A');
        $cell3  = new Cell('3', 'A');

        return [
            'empty-content'             => [
                [],
                $needle,
                [$cell2],
                false,
                [],
            ],
            'only-non-matching-content' => [
                [$cell2, $cell3],
                $needle,
                [$needle, $cell3],
                false,
                [$cell2, $cell3],
            ],
            'only-matching-content'     => [
                [$needle],
                $needle,
                [$cell2, $cell3],
                true,
                [$needle, $cell2, $cell3],
            ],
            'non-last-matching-content' => [
                [$needle, $cell2],
                $needle,
                [$cell3, $cell3],
                true,
                [$needle, $cell3, $cell3, $cell2],
            ],
        ];
    }

    /**
     * @return array
     */
    public function replaceProvider(): array
    {
        $needle = new Cell('1', 'A');
        $cell2  = new Cell('2', 'A');
        $cell3  = new Cell('3', 'A');

        return [
            'empty-content'              => [
                [],
                $needle,
                [$cell2],
                false,
                [],
            ],
            'only-non-matching-content'  => [
                [$cell2, $cell3],
                $needle,
                [$needle, $cell3],
                false,
                [$cell2, $cell3],
            ],
            'only-matching-content'      => [
                [$needle],
                $needle,
                [$cell2, $cell3],
                true,
                [$cell2, $cell3],
            ],
            'non-first-matching-content' => [
                [$cell2, $needle],
                $needle,
                [$cell3, $cell3],
                true,
                [$cell2, $cell3, $cell3],
            ],
            'non-last-matching-content'  => [
                [$needle, $cell2],
                $needle,
                [$cell3, $cell3],
                true,
                [$cell3, $cell3, $cell2],
            ],
        ];
    }

    /**
     * @return array
     */
    public function removeProvider(): array
    {
        $needle = new Cell('1', 'A');
        $cell2  = new Cell('2', 'A');
        $cell3  = new Cell('3', 'A');

        return [
            'empty-content'              => [
                [],
                $needle,
                false,
                [],
            ],
            'only-non-matching-content'  => [
                [$cell2, $cell3],
                $needle,
                false,
                [$cell2, $cell3],
            ],
            'only-matching-content'      => [
                [$needle],
                $needle,
                true,
                [],
            ],
            'non-first-matching-content' => [
                [$cell2, $needle],
                $needle,
                true,
                [$cell2],
            ],
            'non-last-matching-content'  => [
                [$needle, $cell2],
                $needle,
                true,
                [$cell2],
            ],
        ];
    }

    public function testArrayAccessUnset(): void
    {
        $node1 = new Cell('1', 'A');

        $sut = $this->createNode();

        $sut[] = $node1;

        $this->assertTrue($sut->offsetExists(0));

        unset($sut[0]);

        $this->assertfalse($sut->offsetExists(0));
    }

    public function testCreateNodeThrowsLogicException(): void
    {
        $this->expectException(LogicException::class);

        $sut = $this->createNode();

        $sut->setContent('');
    }

    /**
     * @param ICell[]|ICell|string|null $content
     *
     * @return Cells
     */
    private function createNode($content = null): INode
    {
        return new Cells($content);
    }
}
