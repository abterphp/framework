<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Decorator;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\Node;
use PHPUnit\Framework\TestCase;
use stdClass;

class DecoratorTest extends TestCase
{
    /** @var Decorator - System Under Test */
    protected Decorator $sut;

    public function setUp(): void
    {
        $this->sut = $this->getMockForAbstractClass(Decorator::class);
        $this->sut->expects($this->any())->method('init')->willReturnSelf();

        parent::setUp();
    }

    public function testDecorateWithEmptyRulesAndComponents(): void
    {
        $this->sut->decorate([]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithEmptyRules(): void
    {
        $this->sut->decorate([new Component()]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithEmptyComponents(): void
    {
        $this->sut->addRule(new Rule([], null, []));

        $this->sut->decorate([]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateNonMatchingComponents(): void
    {
        $this->sut->addRule(new Rule([], stdClass::class, ['dont-set-this']));

        $this->sut->decorate([new Component()]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithSingleMatchingComponent(): void
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

    public function testDecorateWithIntentClassMap(): void
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

    public function testDecorateWithCallback(): void
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];
        $callback = function (IComponent $component) use ($newClass): void {
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

    public function testDecorateWithCombined(): void
    {
        $defaultClass  = 'foo';
        $mapClass      = 'bar';
        $callbackClass = 'baz';
        $intents       = ['foo', 'bar'];
        $callback      = function (IComponent $component) use ($callbackClass): void {
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

    public function testDecorateMatchingNonTagNodeWorks(): void
    {
        $node = $this->createMock(Node::class);
        $node->expects($this->once())->method('isMatch')->willReturn(true);

        $this->sut->addRule(new Rule([], null, []));

        $this->sut->decorate([$node]);
    }
}
