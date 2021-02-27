<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\I18n\ITranslator;
use Casbin\Enforcer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationTest extends TestCase
{
    protected const INTENTS    = ['bar', 'baz'];
    protected const ATTRIBUTES = ['data-quix' => 'quix'];
    protected const RESOURCE   = 'quint';

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->enforcerMock = $this->createMock(Enforcer::class);
    }

    public function testDefaultGetExtended()
    {
        $sut = new Navigation();

        $expectedResult = [$sut->getPrefix(), $sut->getPostfix()];

        $actualResult = $sut->getExtendedNodes();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetExtendedWithOptionalsReplaced()
    {
        $wrapper = new Component('YYY', [], [], 'foo');
        $prefix  = new Component('XXX', [], [], 'bar');
        $postfix = new Component('ZZZ', [], [], 'baz');

        $sut = new Navigation();

        $sut->setWrapper($wrapper);
        $sut->setPrefix($prefix);
        $sut->setPostfix($postfix);

        $actualResult = $sut->getExtendedNodes();

        $this->assertContains($wrapper, $actualResult);
        $this->assertContains($prefix, $actualResult);
        $this->assertContains($postfix, $actualResult);
    }

    public function testGetDescendantNodesIgnoresOptionals()
    {
        $wrapper = new Component('YYY', [], [], 'foo');
        $prefix  = new Component('XXX', [], [], 'bar');
        $postfix = new Component('ZZZ', [], [], 'baz');

        $sut = new Navigation();

        $sut->setWrapper($wrapper);
        $sut->setPrefix($prefix);
        $sut->setPostfix($postfix);

        $actualResult = $sut->getDescendantNodes(-1);

        $this->assertSame([], $actualResult);
    }

    public function testGetDescendantNodesGetsChildren()
    {
        $node       = new Node('A');
        $component  = new Component($node);
        $collection = new Collection([$component]);
        $item       = new Item($collection);

        $sut = new Navigation();

        $sut->addItem($item);

        $actualResult = $sut->getDescendantNodes(-1);

        $this->assertContains($item, $actualResult);
        $this->assertContains($collection, $actualResult);
        $this->assertContains($component, $actualResult);
        $this->assertContains($node, $actualResult);
    }

    public function testGetDescendantRespectsDepth()
    {
        $node       = new Node('A');
        $component  = new Component($node);
        $collection = new Collection([$component]);
        $item       = new Item($collection);

        $sut = new Navigation();

        $sut->addItem($item);

        $actualResult = $sut->getDescendantNodes(2);

        $this->assertContains($item, $actualResult);
        $this->assertContains($collection, $actualResult);
        $this->assertContains($component, $actualResult);
        $this->assertNotContains($node, $actualResult);
    }

    public function testGetExtendedDescendantNodesIgnoresOptionals()
    {
        $wrapper = new Component(null, [], [], 'foo');
        $prefix  = new Component(null, [], [], 'bar');
        $postfix = new Component(null, [], [], 'baz');

        $sut = new Navigation();

        $sut->setWrapper($wrapper);
        $sut->setPrefix($prefix);
        $sut->setPostfix($postfix);

        $actualResult = $sut->getExtendedDescendantNodes(-1);

        $this->assertSame([$prefix, $postfix, $wrapper], $actualResult);
    }

    public function testSetContent()
    {
        $this->expectException(\LogicException::class);

        $sut = new Navigation();

        $sut->setContent('');
    }

    public function testDefaultToString()
    {
        $sut = new Navigation();

        $this->assertSame('<ul></ul>', (string)$sut);
    }

    public function testToStringWithOptionalsModified()
    {
        $sut = new Navigation();

        $sut->setWrapper(new Component('YYY', [], [], 'foo'));
        $sut->getPrefix()->setContent(new Component('XXX', [], [], 'bar'));
        $sut->getPostfix()->setContent(new Component('ZZZ', [], [], 'baz'));

        $this->assertSame('<bar>XXX</bar><foo><ul></ul></foo><baz>ZZZ</baz>', (string)$sut);
    }

    public function testRenderDisplaysItemsInProperOrder()
    {
        $rawItems = [new Item('AAA'), new Item('BBB'), new Item('CCC'), new Item('DDD')];
        $items    = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];

        $sut = new Navigation(static::INTENTS, static::ATTRIBUTES);

        foreach ($items as $weight => $itemsByWeight) {
            foreach ($itemsByWeight as $item) {
                $sut->addItem($item, $weight);
            }
        }

        $rendered = (string)$sut;

        $this->assertMatchesRegularExpression('/^\s*\<ul.*CCC.*DDD.*AAA.*BBB.*\<\/ul\>\s*$/Ums', $rendered);
    }

    public function testGetWrapperReturnsNullByDefault()
    {
        $sut = new Navigation();

        $actualResult = $sut->getWrapper();

        $this->assertNull($actualResult);
    }

    public function testGetWrapperReturnsLastLestWrapper()
    {
        $sut = new Navigation();

        /** @var IComponent $componentStub */
        $componentStub = $this->createMock(IComponent::class);

        $sut->setWrapper($componentStub);

        $actualResult = $sut->getWrapper();

        $this->assertSame($componentStub, $actualResult);
    }

    public function testSetTranslatorSetsTranslatorOnNodes()
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

        $sut->addItem($itemMock);

        $sut->setTranslator($translatorMock);
    }
}
