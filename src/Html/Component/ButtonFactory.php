<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;
use Opulence\Routing\Urls\UrlException;
use Opulence\Routing\Urls\UrlGenerator;

class ButtonFactory
{
    /** @var UrlGenerator */
    protected UrlGenerator $urlGenerator;

    protected Attributes $iconAttributes;
    protected Attributes $textAttributes;
    protected Attributes $attributes;

    protected string $iconTag = Html5::TAG_I;
    protected string $textTag = Html5::TAG_SPAN;

    /**
     * ButtonFactory constructor.
     *
     * @param UrlGenerator    $urlGenerator
     * @param Attributes|null $textAttributes
     * @param Attributes|null $iconAttributes
     * @param Attributes|null $attributes
     * @param string          $textTag
     * @param string          $iconTag
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        ?Attributes $textAttributes = null,
        ?Attributes $iconAttributes = null,
        ?Attributes $attributes = null,
        string $textTag = Html5::TAG_SPAN,
        string $iconTag = Html5::TAG_I
    ) {
        $this->urlGenerator   = $urlGenerator;
        $this->textAttributes = $textAttributes ?? new Attributes();
        $this->iconAttributes = $iconAttributes ?? new Attributes();
        $this->attributes     = $attributes ?? new Attributes();
        $this->iconTag        = $iconTag;
        $this->textTag        = $textTag;
    }

    /**
     * @param string          $text
     * @param string          $url
     * @param string          $icon
     * @param Attributes|null $textAttributes
     * @param Attributes|null $iconAttributes
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     *
     * @return Button
     */
    public function createFromUrl(
        string $text,
        string $url,
        string $icon = '',
        ?Attributes $textAttributes = null,
        ?Attributes $iconAttributes = null,
        $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = Html5::TAG_A
    ): Button {
        $attributes ??= new Attributes();
        $attributes->replaceItem(new Attribute(Html5::ATTR_HREF, $url));

        if ($icon) {
            return $this->createWithIcon($text, $icon, $textAttributes, $iconAttributes, $intents, $attributes, $tag);
        }

        return $this->createSimple($text, $intents, $attributes, $tag);
    }

    /**
     * // TODO: Create Opulence issue
     *
     * @suppress PhanTypeMismatchArgument issue with Opulence\Routing\Urls\UrlGenerator::createFromName
     *
     * @param string          $text
     * @param string          $urlName
     * @param string[]        $urlArgs
     * @param string          $icon
     * @param Attributes|null $textAttributes
     * @param Attributes|null $iconAttributes
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     *
     * @return Button
     * @throws URLException
     */
    public function createFromName(
        string $text,
        string $urlName,
        array $urlArgs,
        string $icon = '',
        ?Attributes $textAttributes = null,
        ?Attributes $iconAttributes = null,
        $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = Html5::TAG_A
    ): Button {
        $url = $this->urlGenerator->createFromName($urlName, ...$urlArgs);

        $attributes ??= new Attributes();
        $attributes->replaceItem(new Attribute(Html5::ATTR_HREF, $url));

        if ($icon) {
            return $this->createWithIcon($text, $icon, $textAttributes, $iconAttributes, $intents, $attributes, $tag);
        }

        return $this->createSimple($text, $intents, $attributes, $tag);
    }

    /**
     * @param string          $text
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     *
     * @return Button
     */
    public function createSimple(string $text, array $intents, ?Attributes $attributes, ?string $tag): Button
    {
        $attributes = clone $attributes;
        $attributes->merge($this->attributes);

        return new Button($text, $intents, $attributes, $tag);
    }

    /**
     * @param string          $text
     * @param string          $icon
     * @param Attributes|null $textAttributes
     * @param Attributes|null $iconAttributes
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     *
     * @return ButtonWithIcon
     */
    public function createWithIcon(
        string $text,
        string $icon,
        ?Attributes $textAttributes = null,
        ?Attributes $iconAttributes = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ): ButtonWithIcon {
        $iconAttributes ??= new Attributes();
        $iconAttributes = clone $iconAttributes;
        $iconAttributes->merge($this->iconAttributes);

        $textAttributes ??= new Attributes();
        $textAttributes = clone $textAttributes;
        $textAttributes->merge($this->textAttributes);

        $textComponent = new Component($text, [], $textAttributes, $this->textTag);
        $iconComponent = new Component($icon, [], $iconAttributes, $this->iconTag);

        $attributes ??= new Attributes();
        $attributes = clone $attributes;
        $attributes = $attributes->merge($this->attributes);

        return new ButtonWithIcon($textComponent, $iconComponent, $intents, $attributes, $tag);
    }
}
