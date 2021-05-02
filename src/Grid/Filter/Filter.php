<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\Html\Tag;

abstract class Filter extends Tag implements IFilter, ITemplater
{
    public const INTENT_HELP_BLOCK = 'help-block';

    protected const DEFAULT_TAG = Html5::TAG_INPUT;

    /**
     * %1$s - input
     * %2$s - label
     * %3$s - help
     */
    protected const DEFAULT_TEMPLATE = '%1$s %2$s %3$s';

    protected const NAME_PREFIX = 'filter-';

    protected const HELP_CONTENT = 'framework:helpPrefix';

    protected const QUERY_TEMPLATE = '%s = ?';

    protected const PROTECTED_KEYS = [Html5::ATTR_ID, Html5::ATTR_NAME, Html5::ATTR_VALUE, Html5::ATTR_TITLE];

    protected ITag $wrapper;

    protected Label $label;

    protected ITag $helpBlock;

    protected string $fieldName = '';

    /** @var string[] */
    protected array $conditions = [];

    /** @var string[] */
    protected array $queryParams = [];

    protected string $template = self::DEFAULT_TEMPLATE;

    /**
     * Filter constructor.
     *
     * @param string                       $inputName
     * @param string                       $fieldName
     * @param string[]                     $intents
     * @param array<string|Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(
        string $inputName = '',
        string $fieldName = '',
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $this->fieldName = $fieldName;

        $inputName = static::NAME_PREFIX . $inputName;

        $rawAttributes = Attributes::fromArray(
            [
                Html5::ATTR_ID    => $inputName,
                Html5::ATTR_NAME  => $inputName,
                Html5::ATTR_TITLE => '',
                Html5::ATTR_VALUE => '',
            ]
        );

        $attributes ??= [];
        $attributes = Attributes::replace($attributes, $rawAttributes);

        parent::__construct(null, $intents, $attributes, $tag);

        $this->wrapper = new Tag(null, [], null, Html5::TAG_DIV);

        $this->label = new Label($inputName);
        $this->label->setContent($fieldName);

        $this->helpBlock = new Tag(static::HELP_CONTENT, [static::INTENT_HELP_BLOCK], null, Html5::TAG_P);
    }

    protected function getInputName(): string
    {
        return $this->attributes[Html5::ATTR_ID]->getValue();
    }

    protected function getValue(): string
    {
        return $this->attributes[Html5::ATTR_VALUE]->getValue();
    }

    /**
     * @param array<string,string> $params
     *
     * @return IFilter
     */
    public function setParams(array $params): IFilter
    {
        if (empty($params[$this->getInputName()])) {
            return $this;
        }

        $this->attributes[Html5::ATTR_VALUE]->set($params[$this->getInputName()]);

        $this->conditions = [sprintf(static::QUERY_TEMPLATE, $this->fieldName)];

        return $this;
    }

    /**
     * @return array
     */
    public function getWhereConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return string
     */
    public function getQueryPart(): string
    {
        if (empty($this->getValue())) {
            return '';
        }

        return sprintf('%s=%s', $this->getInputName(), urlencode($this->getValue()));
    }

    /**
     * @return ITag
     */
    public function getWrapper(): ITag
    {
        return $this->wrapper;
    }

    /**
     * @return Label|null
     */
    public function getLabel(): ?Label
    {
        return $this->label;
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
        return array_merge([$this->wrapper, $this->label, $this->helpBlock], $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $input = parent::__toString();

        $label = (string)$this->label;

        $help = (string)$this->helpBlock;

        $content = sprintf($this->template, $label, $input, $help);

        $this->wrapper->setContent($content);

        return (string)$this->wrapper;
    }
}
