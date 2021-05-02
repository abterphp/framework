<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Action;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use LogicException;
use Opulence\Orm\IEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function testConstructThrowsExceptionIfAttributeDoesNotExistForAttributeCallback()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Action(null, [], null, ['foo' => fn () => true]);
    }

    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attributes = StubAttributeFactory::createAttributes();
        $str        = Attributes::toString($attributes);

        $callbacks = [
            StubAttributeFactory::ATTRIBUTE_FOO => fn() => [
                StubAttributeFactory::VALUE_FOO,
                StubAttributeFactory::VALUE_BAZ,
            ],
            StubAttributeFactory::ATTRIBUTE_BAR => fn() => StubAttributeFactory::VALUE_BAR_BAZ,
        ];

        return [
            'simple'               => ['Button', null, [], null, null, "<button>Button</button>"],
            'with attributes'      => ['Button', $attributes, [], null, null, "<button$str>Button</button>",],
            'missing translations' => ['Button', null, [], [], null, "<button>Button</button>"],
            'custom tag'           => ['Button', null, [], null, 'mybutton', "<mybutton>Button</mybutton>"],
            'with translations'    => ['Button', null, [], ['Button' => 'Gomb'], null, "<button>Gomb</button>"],
            'with callbacks'       => ['Button', $attributes, $callbacks, null, null, "<button$str>Button</button>",],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param INode[]|INode|string|null $content
     * @param Attribute[]|null          $attributes
     * @param array                     $attributeCallbacks
     * @param string[]|null             $translations
     * @param string|null               $tag
     * @param string                    $expectedResult
     */
    public function testRender(
        $content,
        ?array $attributes,
        array $attributeCallbacks,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createAction($content, $attributes, $attributeCallbacks, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    public function testRenderCallsCallbackWithEntity(): void
    {
        $expectedResult = "<button foo=\"bar\">Button</button>";

        /** @var IEntity|MockObject $entityMock */
        $entityMock = $this->getMockBuilder(IEntity::class)->getMock();
        $entityMock->expects($this->atLeastOnce())->method('getId')->willReturn('bar');

        $content            = 'Button';
        $attributes         = Attributes::fromArray([
            StubAttributeFactory::ATTRIBUTE_FOO => '',
        ]);
        $attributeCallbacks = [
            StubAttributeFactory::ATTRIBUTE_FOO => fn($value, IEntity $entity) => [$entity->getId()],
        ];

        $sut = $this->createAction($content, $attributes, $attributeCallbacks, null, null);

        $sut->setEntity($entityMock);

        $actualResult = (string)$sut;
        $this->assertSame($expectedResult, $actualResult);
    }

    public function testClone(): void
    {
        $attributes = StubAttributeFactory::createAttributes();

        $sut = new Action([new Node('A'), new Tag('B')], [], $attributes);

        $clone = clone $sut;

        $this->assertNotSame($sut, $clone);
        $this->assertEquals($sut, $clone);
        $this->assertCount(2, $clone->getNodes());
    }

    /**
     * @param INode[]|INode|string|null    $content
     * @param array<string,Attribute>|null $attributes
     * @param array                        $attributeCallbacks
     * @param array|null                   $translations
     * @param string|null                  $tag
     *
     * @return Action
     */
    private function createAction(
        $content,
        ?array $attributes,
        array $attributeCallbacks,
        ?array $translations,
        ?string $tag
    ): Action {
        $action = new Action($content, [], $attributes, $attributeCallbacks, $tag);

        $action->setTranslator(MockTranslatorFactory::createSimpleTranslator($this, $translations));

        return $action;
    }

    public function testRemoveAttribute()
    {
        $sut = new Action(null, [], ['foo' => new Attribute('foo')]);

        $sut->removeAttribute('foo');

        $this->assertSame('<button></button>', (string)$sut);
    }

    public function testRemoveAttributeThrowsAttributeWhenRemovingAttributeForAttributeCallback()
    {
        $this->expectException(LogicException::class);

        $sut = new Action(null, [], ['foo' => new Attribute('foo')], ['foo' => fn () => true]);

        $sut->removeAttribute('foo');
    }
}
