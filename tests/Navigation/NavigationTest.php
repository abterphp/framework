<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Node;
use Casbin\Enforcer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationTest extends TestCase
{
    const USERNAME   = 'foo';
    const INTENTS    = ['bar', 'baz'];
    const ATTRIBUTES = ['data-quix' => 'quix'];
    const RESOURCE   = 'quint';

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    public function setUp()
    {
        parent::setUp();

        $this->enforcerMock = $this->getMockBuilder(Enforcer::class)
            ->disableOriginalConstructor()
            ->setMethods(['enforce'])
            ->getMock();
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

    /**
     * @expectedException \LogicException
     */
    public function testSetContent()
    {
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
        $items = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];

        $sut = new Navigation(static::USERNAME, static::INTENTS, static::ATTRIBUTES);

        foreach ($items as $weight => $itemsByWeight) {
            foreach ($itemsByWeight as $item) {
                $sut->addItem($item, $weight);
            }
        }

        $rendered = (string)$sut;

        $this->assertRegexp('/^\s*\<ul.*CCC.*DDD.*AAA.*BBB.*\<\/ul\>\s*$/Ums', $rendered);
    }

    public function testEnforcerIsIgnoredIfNoResourceIsRequired()
    {
        $rawItems = [new Item('AAA'), new Item('BBB'), new Item('CCC'), new Item('DDD')];
        $items = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];

        $this->enforcerMock->expects($this->any())->method('enforce')->willReturn(false);

        $sut = new Navigation(static::USERNAME, static::INTENTS, static::ATTRIBUTES, $this->enforcerMock);

        foreach ($items as $weight => $itemsByWeight) {
            foreach ($itemsByWeight as $item) {
                $sut->addItem($item, $weight);
            }
        }

        $nodes = $sut->getNodes();

        $this->assertContains($rawItems[0], $nodes);
        $this->assertContains($rawItems[1], $nodes);
        $this->assertContains($rawItems[2], $nodes);
        $this->assertContains($rawItems[3], $nodes);
    }

    public function testAddingItemIsSkippedIfResourceIsSetButEnforcerIsMissing()
    {
        $rawItems = [new Item('AAA'), new Item('BBB'), new Item('CCC'), new Item('DDD')];
        $items = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];

        $sut = new Navigation(static::USERNAME, static::INTENTS, static::ATTRIBUTES);

        foreach ($items as $weight => $itemsByWeight) {
            foreach ($itemsByWeight as $item) {
                $sut->addItem($item, $weight, static::RESOURCE);
            }
        }

        $nodes = $sut->getNodes();

        $this->assertSame([], $nodes);
    }

    public function testAddingItemIsSkippedIfResourceIsSetButEnforcerThrowsException()
    {
        $rawItems = [new Item('AAA'), new Item('BBB'), new Item('CCC'), new Item('DDD')];
        $items = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];

        $this->enforcerMock->expects($this->at(0))->method('enforce')->willThrowException(new \Exception());

        $sut = new Navigation(static::USERNAME, static::INTENTS, static::ATTRIBUTES, $this->enforcerMock);

        foreach ($items as $weight => $itemsByWeight) {
            foreach ($itemsByWeight as $item) {
                $sut->addItem($item, $weight, static::RESOURCE);
            }
        }

        $nodes = $sut->getNodes();

        $this->assertSame([], $nodes);
    }

    public function testEnforcerSilentlySkipsItemsNotAllowed()
    {
        $rawItems = [new Item('AAA'), new Item('BBB'), new Item('CCC'), new Item('DDD')];
        $items = [
            100 => [$rawItems[0], $rawItems[1]],
            50  => [$rawItems[2]],
            75  => [$rawItems[3]],
        ];

        $this->enforcerMock->expects($this->at(0))->method('enforce')->willReturn(false);
        $this->enforcerMock->expects($this->at(1))->method('enforce')->willReturn(true);
        $this->enforcerMock->expects($this->at(2))->method('enforce')->willReturn(true);
        $this->enforcerMock->expects($this->at(3))->method('enforce')->willReturn(true);

        $sut = new Navigation(static::USERNAME, static::INTENTS, static::ATTRIBUTES, $this->enforcerMock);

        foreach ($items as $weight => $itemsByWeight) {
            foreach ($itemsByWeight as $item) {
                $sut->addItem($item, $weight, static::RESOURCE);
            }
        }

        $nodes = $sut->getNodes();

        $this->assertNotContains($rawItems[0], $nodes);
        $this->assertContains($rawItems[1], $nodes);
        $this->assertContains($rawItems[2], $nodes);
        $this->assertContains($rawItems[3], $nodes);
    }
}
