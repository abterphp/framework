<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Decorator;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class DecoratorTest extends TestCase
{
    /** @var Decorator|MockObject - System Under Test */
    protected $sut;

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
        $this->sut->decorate([new Tag()]);

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

        $this->sut->decorate([new Tag()]);

        $this->assertTrue(true, 'No error was found.');
    }

    public function testDecorateWithSingleMatchingComponent(): void
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];

        $nonMatchingComponent = new Tag('a');
        $matchingComponent    = new Tag('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [$newClass]));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertTrue($matchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertContains($newClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS)->getValues());
    }

    public function testDecorateWithIntentClassMap(): void
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];

        $nonMatchingComponent = new Tag('a');
        $matchingComponent    = new Tag('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [], ['bar' => [$newClass]]));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertTrue($matchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertContains($newClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS)->getValues());
    }

    public function testDecorateWithCallback(): void
    {
        $newClass = 'baz';
        $intents  = ['foo', 'bar'];
        $callback = function (ITag $component) use ($newClass): void {
            $component->appendToClass($newClass);
        };

        $nonMatchingComponent = new Tag('a');
        $matchingComponent    = new Tag('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [], [], $callback));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertTrue($matchingComponent->hasAttribute(Html5::ATTR_CLASS));

        $this->assertEquals($newClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS)->getValue());
    }

    public function testDecorateWithCombined(): void
    {
        $defaultClass  = 'foo';
        $mapClass      = 'bar';
        $callbackClass = 'baz';
        $intents       = ['foo', 'bar'];
        $callback      = function (ITag $component) use ($callbackClass): void {
            $component->appendToClass($callbackClass);
        };

        $nonMatchingComponent = new Tag('a');
        $matchingComponent    = new Tag('b', $intents);

        $this->sut->addRule(new Rule($intents, null, [$defaultClass], ['foo' => [$mapClass]], $callback));

        $this->sut->decorate([$nonMatchingComponent, $matchingComponent]);

        $this->assertFalse($nonMatchingComponent->hasAttribute(Html5::ATTR_CLASS));
        $this->assertContains($defaultClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS)->getValues());
        $this->assertContains($mapClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS)->getValues());
        $this->assertContains($callbackClass, $matchingComponent->getAttribute(Html5::ATTR_CLASS)->getValues());
    }

    public function testDecorateMatchingNonTagNodeWorks(): void
    {
        $node = $this->createMock(Node::class);
        $node->expects($this->once())->method('isMatch')->willReturn(true);

        $this->sut->addRule(new Rule([], null, []));

        $this->sut->decorate([$node]);
    }
}
