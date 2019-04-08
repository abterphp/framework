<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Constant\Html5;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ButtonFactoryTest extends TestCase
{
    /** @var UrlGenerator|MockObject */
    protected $urlGeneratorMock;

    /** @var ButtonFactory */
    protected $sut;

    /** @var string[][] */
    protected $iconAttributes = [];

    /** @var string[][] */
    protected $textAttributes = [];

    public function setUp()
    {
        $this->urlGeneratorMock = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFromName'])
            ->getMock();

        $this->iconAttributes = StubAttributeFactory::createAttributes(['icon' => ['asd']]);
        $this->textAttributes = StubAttributeFactory::createAttributes(['text' => ['qwe']]);

        $this->sut = new ButtonFactory($this->urlGeneratorMock, $this->textAttributes, $this->iconAttributes);
    }

    public function testCreateFromUrlCreatesSimpleButtonByDefault()
    {
        $url  = '/best/url/ever';
        $text = 'button text';

        $actualResult = $this->sut->createFromUrl($text, $url);

        $this->assertInstanceOf(Button::class, $actualResult);
        $this->assertNotInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromUrlCanCreateButtonWithIcon()
    {
        $url  = '/best/url/ever';
        $text = 'button text';
        $icon = 'hello';

        $actualResult = $this->sut->createFromUrl($text, $url, $icon);

        $this->assertInstanceOf(Button::class, $actualResult);
        $this->assertInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromUrlCanCreateComplexButtonWithIcon()
    {
        $url         = '/best/url/ever';
        $text        = 'button text';
        $icon        = 'hello';
        $textAttribs = ['attr5' => ['val7', 'val8'], 'attr6' => ['val9']];
        $iconAttribs = ['attr3' => ['val4', 'val5'], 'attr4' => ['val6']];
        $attribs     = ['attr1' => ['val1', 'val2'], 'attr2' => ['val3']];

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

        $expectedResult = '<strong attr1="val1 val2" attr2="val3" href="/best/url/ever"><i foo="foo baz" bar="bar baz" icon="asd" attr3="val4 val5" attr4="val6">hello</i> <span foo="foo baz" bar="bar baz" text="qwe" attr5="val7 val8" attr6="val9">button text</span></strong>'; // nolint

        $this->assertSame($expectedResult, (string)$actualResult);
    }

    public function testCreateFromNameCreatesSimpleButtonByDefault()
    {
        $name = 'best-route-ever';
        $text = 'button text';

        $this->urlGeneratorMock
            ->expects($this->atLeastOnce())
            ->method('createFromName')
            ->willReturn('/best/url/ever');

        $actualResult = $this->sut->createFromName($text, $name, []);

        $this->assertInstanceOf(Button::class, $actualResult);
        $this->assertNotInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromNameCanCreateButtonWithIcon()
    {
        $name = 'best-route-ever';
        $text = 'button text';
        $icon = 'hello';

        $this->urlGeneratorMock
            ->expects($this->atLeastOnce())
            ->method('createFromName')
            ->willReturn('/best/url/ever');

        $actualResult = $this->sut->createFromName($text, $name, [], $icon);

        $this->assertInstanceOf(Button::class, $actualResult);
        $this->assertInstanceOf(ButtonWithIcon::class, $actualResult);
    }

    public function testCreateFromNameCanCreateComplexButtonWithIcon()
    {
        $name = 'best-route-ever';
        $text        = 'button text';
        $icon        = 'hello';
        $textAttribs = ['attr5' => ['val7', 'val8'], 'attr6' => ['val9']];
        $iconAttribs = ['attr3' => ['val4', 'val5'], 'attr4' => ['val6']];
        $attribs     = ['attr1' => ['val1', 'val2'], 'attr2' => ['val3']];

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

        $expectedResult = '<strong attr1="val1 val2" attr2="val3" href="/best/url/ever"><i foo="foo baz" bar="bar baz" icon="asd" attr3="val4 val5" attr4="val6">hello</i> <span foo="foo baz" bar="bar baz" text="qwe" attr5="val7 val8" attr6="val9">button text</span></strong>'; // nolint

        $this->assertSame($expectedResult, (string)$actualResult);
    }
}
