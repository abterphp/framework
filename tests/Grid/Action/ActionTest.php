<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Action;

use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\I18n\MockTranslatorFactory;
use Opulence\Orm\IEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        $attributes = StubAttributeFactory::createAttributes();
        $str        = ArrayHelper::toAttributes($attributes);

        $callbacks = [
            StubAttributeFactory::ATTRIBUTE_FOO => function () {
                return [StubAttributeFactory::VALUE_FOO, StubAttributeFactory::VALUE_BAZ];
            },
            StubAttributeFactory::ATTRIBUTE_BAR => function () {
                return StubAttributeFactory::VALUE_BAR_BAZ;
            },
        ];

        return [
            'simple'               => ['Button', [], [], null, null, "<button>Button</button>"],
            'with attributes'      => ['Button', $attributes, [], null, null, "<button$str>Button</button>"],
            'missing translations' => ['Button', [], [], [], null, "<button>Button</button>"],
            'custom tag'           => ['Button', [], [], null, 'mybutton', "<mybutton>Button</mybutton>"],
            'with translations'    => ['Button', [], [], ['Button' => 'Gomb'], null, "<button>Gomb</button>"],
            'with callbacks'       => ['Button', [], $callbacks, null, null, "<button$str>Button</button>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param INode[]|INode|string|null $content
     * @param array                     $attributes
     * @param array                     $attributeCallbacks
     * @param string[]|null             $translations
     * @param string|null               $tag
     * @param string                    $expectedResult
     */
    public function testRender(
        $content,
        array $attributes,
        array $attributeCallbacks,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($content, $attributes, $attributeCallbacks, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    public function testRenderCallsCallbackWithEntity()
    {
        $expectedResult = "<button foo=\"bar\">Button</button>";

        /** @var IEntity|MockObject $entityMock */
        $entityMock = $this->getMockBuilder(IEntity::class)
            ->setMethods(['getId', 'setId'])
            ->getMock();
        $entityMock->expects($this->atLeastOnce())->method('getId')->willReturn('bar');

        $content            = 'Button';
        $attributes         = [];
        $attributeCallbacks = [
            StubAttributeFactory::ATTRIBUTE_FOO => function ($value, IEntity $entity) {
                return [$entity->getId()];
            },
        ];

        $sut = $this->createElement($content, $attributes, $attributeCallbacks, null, null);

        $sut->setEntity($entityMock);

        $actualResult = (string)$sut;
        $this->assertSame($expectedResult, $actualResult);
    }

    public function testDuplicate()
    {
        $attributes = StubAttributeFactory::createAttributes();

        $sut = new Action([new Node('A'), new Collection('B')], [], $attributes);

        $clone = $sut->duplicate();

        $this->assertNotSame($sut, $clone);
        $this->assertEquals($sut, $clone);
        $this->assertCount(2, $clone);
    }

    /**
     * @param INode[]|INode|string|null $content
     * @param array                     $attributes
     * @param array                     $attributeCallbacks
     * @param array|null                $translations
     * @param string|null               $tag
     *
     * @return Action
     */
    private function createElement(
        $content,
        array $attributes,
        array $attributeCallbacks,
        ?array $translations,
        ?string $tag
    ): Action {
        $action = new Action($content, [], $attributes, $attributeCallbacks, $tag);

        $action->setTranslator(MockTranslatorFactory::createSimpleTranslator($this, $translations));

        return $action;
    }
}
