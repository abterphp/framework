<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use Opulence\Routing\Urls\UrlException;
use Opulence\Routing\Urls\UrlGenerator;

class ButtonFactory
{
    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var array */
    protected $iconAttributes = [];

    /** @var array */
    protected $textAttributes = [];

    /** @var array */
    protected $attributes = [];

    /** @var string */
    protected $iconTag = Html5::TAG_I;

    /** @var string */
    protected $textTag = Html5::TAG_SPAN;

    /**
     * ButtonFactory constructor.
     *
     * @param UrlGenerator $urlGenerator
     * @param string[][]   $textAttributes
     * @param string[][]   $iconAttributes
     * @param string[][]   $attributes
     * @param string       $textTag
     * @param string       $iconTag
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        array $textAttributes = [],
        array $iconAttributes = [],
        array $attributes = [],
        string $textTag = Html5::TAG_SPAN,
        string $iconTag = Html5::TAG_I
    ) {
        $this->urlGenerator   = $urlGenerator;
        $this->textAttributes = $textAttributes;
        $this->iconAttributes = $iconAttributes;
        $this->attributes     = $attributes;
        $this->iconTag        = $iconTag;
        $this->textTag        = $textTag;
    }

    /**
     * @param string      $text
     * @param string      $url
     * @param string      $icon
     * @param string[][]  $textAttribs
     * @param string[][]  $iconAttribs
     * @param string[]    $intents
     * @param string[][]  $attribs
     * @param string|null $tag
     *
     * @return Button
     */
    public function createFromUrl(
        string $text,
        string $url,
        string $icon = '',
        array $textAttribs = [],
        array $iconAttribs = [],
        $intents = [],
        $attribs = [],
        ?string $tag = Html5::TAG_A
    ): Button {
        $attribs[Html5::ATTR_HREF] = [$url];

        if ($icon) {
            return $this->createWithIcon($text, $icon, $textAttribs, $iconAttribs, $intents, $attribs, $tag);
        }

        return $this->createSimple($text, $intents, $attribs, $tag);
    }

    /**
     * // TODO: Create Opulence issue
     * @suppress PhanTypeMismatchArgument issue with Opulence\Routing\Urls\UrlGenerator::createFromName
     *
     * @param string      $text
     * @param string      $urlName
     * @param string[]    $urlArgs
     * @param string      $icon
     * @param string[][]  $textAttribs
     * @param string[][]  $iconAttribs
     * @param string[]    $intents
     * @param string[][]  $attribs
     * @param string|null $tag
     *
     * @return Button
     * @throws URLException
     */
    public function createFromName(
        string $text,
        string $urlName,
        array $urlArgs,
        string $icon = '',
        array $textAttribs = [],
        array $iconAttribs = [],
        $intents = [],
        $attribs = [],
        ?string $tag = Html5::TAG_A
    ): Button {
        $url = $this->urlGenerator->createFromName($urlName, ...$urlArgs);

        $attribs[Html5::ATTR_HREF] = [$url];

        if ($icon) {
            return $this->createWithIcon($text, $icon, $textAttribs, $iconAttribs, $intents, $attribs, $tag);
        }

        return $this->createSimple($text, $intents, $attribs, $tag);
    }

    /**
     * @param string      $text
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     *
     * @return Button
     */
    public function createSimple(string $text, array $intents, array $attributes, ?string $tag): Button
    {
        $attributes = ArrayHelper::unsafeMergeAttributes($this->attributes, $attributes);

        $linkComponent = new Button($text, $intents, $attributes, $tag);

        return $linkComponent;
    }

    /**
     * @param string      $text
     * @param string      $icon
     * @param string[][]  $textAttribs
     * @param string[][]  $iconAttribs
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     *
     * @return ButtonWithIcon
     */
    public function createWithIcon(
        string $text,
        string $icon,
        array $textAttribs,
        array $iconAttribs,
        array $intents,
        $attributes,
        ?string $tag
    ): ButtonWithIcon {
        $iconAttribs = ArrayHelper::unsafeMergeAttributes($this->iconAttributes, $iconAttribs);
        $textAttribs = ArrayHelper::unsafeMergeAttributes($this->textAttributes, $textAttribs);

        $textComponent = new Component($text, [], $textAttribs, $this->textTag);
        $iconComponent = new Component($icon, [], $iconAttribs, $this->iconTag);

        $attributes = ArrayHelper::unsafeMergeAttributes($this->attributes, $attributes);

        $linkComponent = new ButtonWithIcon($textComponent, $iconComponent, $intents, $attributes, $tag);

        return $linkComponent;
    }
}
