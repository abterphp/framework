<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Component\Button;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\I18n\ITranslator;

class Sortable extends Cell implements ICell, ITemplater
{
    const DEFAULT_TAG = Html5::TAG_TH;

    /**
     * %1$s - nodes
     * %2$s - sort button
     */
    const DEFAULT_TEMPLATE = '%1$s %2$s';

    const NAME_PREFIX = 'sort-';

    const DIR_ASC  = 'ASC';
    const DIR_DESC = 'DESC';

    const BTN_INTENT_SHOARTING    = 'grid-sortable-shoarting';
    const BTN_INTENT_CARET_DOWN   = 'caret-down';
    const BTN_INTENT_CARET_UP     = 'caret-up';
    const BTN_INTENT_CARET_ACTIVE = 'caret-active';

    /** @var string */
    protected $template = self::DEFAULT_TEMPLATE;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $fieldName = '';

    /** @var string */
    protected $inputName = '';

    /** @var array */
    protected $sortConditions = [];

    /** @var int */
    protected $value = 0;

    /** @var Button */
    protected $sortBtn;

    /**
     * Sortable constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string                    $group
     * @param string                    $inputName
     * @param string                    $fieldName
     * @param string[]                  $intents
     * @param array                     $attributes
     * @param string|null               $tag
     */
    public function __construct(
        $content,
        string $group,
        string $inputName,
        string $fieldName,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct($content, $group, $intents, $attributes, $tag);

        $this->fieldName = $fieldName;
        $this->inputName = static::NAME_PREFIX . $inputName;

        $this->sortBtn = new Component(null, [static::BTN_INTENT_SHOARTING], [], Html5::TAG_A);
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): Sortable
    {
        $this->baseUrl = $baseUrl;

        $this->setSortBtnDirection();

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string|null
     */
    public function getQueryParam(): ?string
    {
        if ($this->value === 0) {
            return null;
        }

        return sprintf('%s=%s&', $this->inputName, $this->value);
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params): Sortable
    {
        if (!array_key_exists($this->inputName, $params)) {
            return $this->setSortBtnDirection();
        }

        $this->value = (int)$params[$this->inputName];

        if ($this->value === 0) {
            return $this->setSortBtnDirection();
        }

        $dir = $this->value > 0 ? static::DIR_ASC : static::DIR_DESC;

        $this->sortConditions = [sprintf('%s %s', $this->fieldName, $dir)];

        return $this->setSortBtnDirection();
    }

    /**
     * @return string
     */
    protected function setSortBtnDirection(): Sortable
    {
        if ($this->value === 0) {
            $this->sortBtn->addIntent(static::BTN_INTENT_CARET_DOWN);
            $dir = '1';
        } elseif ($this->value > 0) {
            $this->sortBtn->addIntent(static::BTN_INTENT_CARET_DOWN, static::BTN_INTENT_CARET_ACTIVE);
            $dir = '-1';
        } else {
            $this->sortBtn->addIntent(static::BTN_INTENT_CARET_UP, static::BTN_INTENT_CARET_ACTIVE);
            $dir = '0';
        }

        $href = sprintf('%s%s=%s', $this->baseUrl, $this->inputName, $dir);
        $this->sortBtn->setAttribute(Html5::ATTR_HREF, $href);

        return $this;
    }

    /**
     * @return Component
     */
    public function getSortBtn(): Component
    {
        return $this->sortBtn;
    }

    /**
     * @return array
     */
    public function getSortConditions(): array
    {
        return $this->sortConditions;
    }

    /**
     * @return string
     */
    public function getQueryPart(): string
    {
        if ($this->value === 0) {
            return '';
        }

        return sprintf('%s=%s', $this->inputName, $this->value);
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate(string $template): INode
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return array_merge([$this->sortBtn], $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $nodes = Collection::__toString();

        $content = sprintf(
            $this->template,
            $nodes,
            (string)$this->sortBtn
        );

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);

        return $content;
    }
}
