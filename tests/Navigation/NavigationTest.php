<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\I18n\ITranslator;
use Casbin\Enforcer;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationTest extends TestCase
{
    protected const INTENTS    = ['bar', 'baz'];
    protected const ATTRIBUTES = ['data-quix' => ['quix']];
    protected const RESOURCE   = 'quint';

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->enforcerMock = $this->createMock(Enforcer::class);
    }

    public function testAddWillAddAtTheEnd(): void
    {
        $expectedResult = "<ul>2\n0\n0\n0\n1\n1\n2</ul>";

        $itemMock0 = $this->createMock(Item::class);
        $itemMock0->expects($this->any())->method('__toString')->willReturn('0');
        $itemMock1 = $this->createMock(Item::class);
        $itemMock1->expects($this->any())->method('__toString')->willReturn('1');
        $itemMock2 = $this->createMock(Item::class);
        $itemMock2->expects($this->any())->method('__toString')->willReturn('2');

        $sut = new Navigation();
        $sut->addWithWeight(2, $itemMock0, $itemMock0);
        $sut->addWithWeight(4, $itemMock1, $itemMock1);
        $sut->addWithWeight(1, $itemMock2);
        $sut->addWithWeight(3, $itemMock0);
        $sut->add($itemMock2);

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetExtendedDefault(): void
    {
        $sut = new Navigation();

        $expectedResult = [$sut->getPrefix(), $sut->getPostfix()];

        $actualResult = $sut->getExtendedNodes();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetExtendedWithWrapper(): void
    {
        $prefix = new Node();
        $postfix = new Node();
        $wrapper = new Tag();

        $sut = new Navigation();
        $sut->setWrapper($wrapper)->setPrefix($prefix)->setPostfix($postfix);

        $expectedResult = [$prefix, $postfix, $wrapper];

        $actualResult = $sut->getExtendedNodes();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetContent(): void
    {
        $this->expectException(\LogicException::class);

        $sut = new Navigation();

        /** @scrutinizer ignore-deprecated */
        $sut->setContent('');
    }

    public function testSetContentReturnsSelfIfContentIsNull(): void
    {
        $sut = new Navigation();

        $actualResult = $sut->setContent(null);

        $this->assertSame($sut, $actualResult);
    }

    public function testDefaultToString(): void
    {
        $expectedResult = '<ul></ul>';

        $sut = new Navigation();

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testToStringWithOptionalsModified(): void
    {
        $sut = new Navigation();

        $sut->setWrapper(new Tag('YYY', [], null, 'foo'));
        $sut->getPrefix()->setContent(new Tag('XXX', [], null, 'bar'));
        $sut->getPostfix()->setContent(new Tag('ZZZ', [], null, 'baz'));

        $this->assertSame('<bar>XXX</bar><foo><ul></ul></foo><baz>ZZZ</baz>', (string)$sut);
    }

    public function testRenderDisplaysItemsInProperOrder(): void
    {
        $rawItems   = [new Item('AAA'), new Item('BBB'), new Item('CCC'), new Item('DDD')];
        $items      = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];
        $attributes = Attributes::fromArray(static::ATTRIBUTES);

        $sut = new Navigation(static::INTENTS, $attributes);

        foreach ($items as $weight => $itemsByWeight) {
            $sut->addWithWeight($weight, ...$itemsByWeight);
        }

        $rendered = (string)$sut;

        $this->assertMatchesRegularExpression('/^\s*\<ul.*CCC.*DDD.*AAA.*BBB.*\<\/ul\>\s*$/Ums', $rendered);
    }

    public function testGetWrapperReturnsNullByDefault(): void
    {
        $sut = new Navigation();

        $actualResult = $sut->getWrapper();

        $this->assertNull($actualResult);
    }

    public function testGetWrapperReturnsLastLestWrapper(): void
    {
        $sut = new Navigation();

        /** @var ITag $tagMock */
        $tagMock = $this->createMock(ITag::class);

        $sut->setWrapper($tagMock);

        $actualResult = $sut->getWrapper();

        $this->assertSame($tagMock, $actualResult);
    }

    public function testSetTranslatorSetsTranslatorOnNodes(): void
    {
        $sut = new Navigation();

        /** @var ITranslator $translatorMock */
        $translatorMock = $this->createMock(ITranslator::class);

        /** @var Item|MockObject $itemMock */
        $itemMock = $this->createMock(Item::class);

        $itemMock
            ->expects($this->atLeastOnce())
            ->method('setTranslator')
            ->with($translatorMock)
            ->willReturn($sut);

        $sut[] = $itemMock;

        $sut->setTranslator($translatorMock);
    }

    public function testOffsetSetThrowsExceptionOnInvalidOffset(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $sut = new Navigation();

        /** @var Item|MockObject $itemMock */
        $itemMock = $this->createMock(Item::class);

        $sut[1] = $itemMock;
    }

    public function testOffsetSetCanUseWeightIfNeeded(): void
    {
        $sut = $this->createNavigation();

        $itemMock = $this->createMock(Item::class);
        $itemMock->expects($this->any())->method('__toString')->willReturn('!');
        $sut[4] = $itemMock;

        $expectedResult = "<ul>ax\nay\naz\nbx\n!\nbz</ul>";
        $actualResult   = (string)$sut;

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testOffsetUnSetCanUseWeightIfNeeded(): void
    {
        $sut = $this->createNavigation();

        unset($sut[4]);

        $expectedResult = "<ul>ax\nay\naz\nbx\nbz</ul>";
        $actualResult   = (string)$sut;

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testOffsetGetCanUseWeightIfNeeded(): void
    {
        $sut            = $this->createNavigation();
        $expectedResult = "by";
        $actualResult   = (string)$sut[4];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testCount(): void
    {
        $sut = $this->createNavigation();

        $expectedResult = 6;
        $actualResult   = count($sut);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testForeach(): void
    {
        $sut = $this->createNavigation();

        $expectedResults = ['ax', 'ay', 'az', 'bx', 'by', 'bz'];

        foreach ($sut as $key => $item) {
            $this->assertEquals($expectedResults[$key], (string)$item);
        }
    }

    /**
     * @param array|string[] $weightGroups
     * @param array|string[] $itemGroups
     *
     * @return Navigation
     */
    protected function createNavigation(
        array $weightGroups = ['a', 'b'],
        array $itemGroups = ['x', 'y', 'z']
    ): Navigation {
        $sut = new Navigation();
        foreach ($weightGroups as $w) {
            foreach ($itemGroups as $n) {
                $itemMock = $this->createMock(Item::class);
                $itemMock->expects($this->any())->method('__toString')->willReturn("$w$n");
                $sut->addWithWeight(ord($w) - ord('a'), $itemMock);
            }
        }

        return $sut;
    }
}
