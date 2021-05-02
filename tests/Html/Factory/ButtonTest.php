<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Factory;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Component\Button as ButtonComponent;
use AbterPhp\Framework\Html\Component\ButtonWithIcon;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{
    /** @var Button - System Under Test */
    protected Button $sut;

    /** @var UrlGenerator|MockObject */
    protected $urlGeneratorMock;

    /** @var array<string,Attribute> */
    protected array $iconAttributes;

    /** @var array<string,Attribute> */
    protected array $textAttributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->urlGeneratorMock = $this->createMock(UrlGenerator::class);

        $this->iconAttributes = StubAttributeFactory::createAttributes(['icon' => ['asd']]);
        $this->textAttributes = StubAttributeFactory::createAttributes(['text' => ['qwe']]);

        $this->sut = new Button($this->urlGeneratorMock, $this->textAttributes, $this->iconAttributes);
    }

    public function testCreateFromUrlCreatesSimpleButtonByDefault(): void
    {
        $url  = '/best/url/ever';
        $text = 'button text';

        $actualResult = $this->sut->createFromUrl($text, $url);

        $this->assertInstanceOf(ButtonComponent::class, $actualResult);
        $this->assertNotInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromUrlCanCreateButtonWithIcon(): void
    {
        $url  = '/best/url/ever';
        $text = 'button text';
        $icon = 'hello';

        $actualResult = $this->sut->createFromUrl($text, $url, $icon);

        $this->assertInstanceOf(ButtonComponent::class, $actualResult);
        $this->assertInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromUrlCanCreateComplexButtonWithIcon(): void
    {
        $url         = '/best/url/ever';
        $text        = 'button text';
        $icon        = 'hello';
        $textAttribs = Attributes::fromArray(['attr5' => ['val7', 'val8'], 'attr6' => ['val9']]);
        $iconAttribs = Attributes::fromArray(['attr3' => ['val4', 'val5'], 'attr4' => ['val6']]);
        $attribs     = Attributes::fromArray(['attr1' => ['val1', 'val2'], 'attr2' => ['val3']]);

        $actualResult = $this->sut->createFromUrl(
            $text,
            $url,
            $icon,
            $textAttribs,
            $iconAttribs,
            [],
            $attribs,
            Html5::TAG_STRONG
        );

        $expectedResult = '<strong attr1="val1 val2" attr2="val3" href="/best/url/ever"><i attr3="val4 val5" attr4="val6" foo="foo baz" bar="bar baz" icon="asd">hello</i> <span attr5="val7 val8" attr6="val9" foo="foo baz" bar="bar baz" text="qwe">button text</span></strong>'; // phpcs:ignore

        $this->assertSame($expectedResult, (string)$actualResult);
    }

    public function testCreateFromNameCreatesSimpleButtonByDefault(): void
    {
        $name = 'best-route-ever';
        $text = 'button text';

        $this->urlGeneratorMock
            ->expects($this->atLeastOnce())
            ->method('createFromName')
            ->willReturn('/best/url/ever');

        $actualResult = $this->sut->createFromName($text, $name, []);

        $this->assertInstanceOf(ButtonComponent::class, $actualResult);
        $this->assertNotInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromNameCanCreateButtonWithIcon(): void
    {
        $name = 'best-route-ever';
        $text = 'button text';
        $icon = 'hello';

        $this->urlGeneratorMock
            ->expects($this->atLeastOnce())
            ->method('createFromName')
            ->willReturn('/best/url/ever');

        $actualResult = $this->sut->createFromName($text, $name, [], $icon);

        $this->assertInstanceOf(ButtonComponent::class, $actualResult);
        $this->assertInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromNameCanCreateComplexButtonWithIcon(): void
    {
        $name        = 'best-route-ever';
        $text        = 'button text';
        $icon        = 'hello';
        $textAttribs = Attributes::fromArray(['attr5' => ['val7', 'val8'], 'attr6' => ['val9']]);
        $iconAttribs = Attributes::fromArray(['attr3' => ['val4', 'val5'], 'attr4' => ['val6']]);
        $attribs     = Attributes::fromArray(['attr1' => ['val1', 'val2'], 'attr2' => ['val3']]);

        $this->urlGeneratorMock
            ->expects($this->atLeastOnce())
            ->method('createFromName')
            ->willReturn('/best/url/ever');

        $actualResult = $this->sut->createFromName(
            $text,
            $name,
            [],
            $icon,
            $textAttribs,
            $iconAttribs,
            [],
            $attribs,
            Html5::TAG_STRONG
        );

        $expectedResult = '<strong attr1="val1 val2" attr2="val3" href="/best/url/ever"><i attr3="val4 val5" attr4="val6" foo="foo baz" bar="bar baz" icon="asd">hello</i> <span attr5="val7 val8" attr6="val9" foo="foo baz" bar="bar baz" text="qwe">button text</span></strong>'; // phpcs:ignore

        $this->assertSame($expectedResult, (string)$actualResult);
    }
}
