<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Decorator;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\Node;
use PHPUnit\Framework\TestCase;

class DecoratorTest extends TestCase
{
    /** @var Decorator */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = $this->getMockForAbstractClass(Decorator::class);
        $this->sut->expects($this->any())->method('init')->willReturnSelf();

        parent::setUp();
    }

    public function testDecorateWithEmptyRulesAndComponents()
    {
        $this->sut->decorate([]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithEmptyRules()
    {
        $this->sut->decorate([new Component()]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithEmptyComponents()
    {
        $this->sut->addRule(new Rule([], null, []));

        $this->sut->decorate([]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateNonMatchingComponents()
    {
        $this->sut->addRule(new Rule([], \stdClass::class, ['dont-set-this']));

        $this->sut->decorate([new Component()]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithSingleMatchingComponent()
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];

        $nonMatchingComponent = new Component('a');
        $matchingComponent    = new Component('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [$newClass]));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertTrue($matchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertStringContainsString($newClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS));
    }

    public function testDecorateWithIntentClassMap()
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];

        $nonMatchingComponent = new Component('a');
        $matchingComponent    = new Component('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [], ['bar' => [$newClass]]));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertTrue($matchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertStringContainsString($newClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS));
    }

    public function testDecorateWithCallback()
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];
        $callback = function (IComponent $component) use ($newClass) {
            $component->appendToClass($newClass);
        };

        $nonMatchingComponent = new Component('a');
        $matchingComponent    = new Component('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [], [], $callback));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertTrue($matchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertStringContainsString($newClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS));
    }

    public function testDecorateWithCombined()
    {
        $defaultClass  = 'foo';
        $mapClass      = 'bar';
        $callbackClass = 'baz';
        $intents       = ['foo', 'bar'];
        $callback      = function (IComponent $component) use ($callbackClass) {
            $component->appendToClass($callbackClass);
        };

        $nonMatchingComponent = new Component('a');
        $matchingComponent    = new Component('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [$defaultClass], ['foo' => [$mapClass]], $callback));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertStringContainsString($defaultClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS));
        $this->assertStringContainsString($mapClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS));
        $this->assertStringContainsString($callbackClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS));
    }

    public function testDecorateMatchingNonTagNodeWorks()
    {
        $node = $this->getMockBuilder(Node::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMatch'])
            ->getMock();
        $node->expects($this->once())->method('isMatch')->willReturn(true);

        $this->sut->addRule(new Rule([], null, []));

        $this->sut->decorate([$node]);
    }
}
