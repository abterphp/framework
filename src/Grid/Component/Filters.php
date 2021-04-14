<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Filter\IFilter;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;

class Filters extends Component implements ITemplater
{
    protected const DEFAULT_TAG = Html5::TAG_FORM;

    public const BTN_CONTENT_FILTERS = 'framework:filters';
    public const BTN_CONTENT_FILTER  = 'framework:filter';
    public const BTN_CONTENT_RESET   = 'framework:reset';

    protected const ATTRIBUTES_FORM = [
        Html5::ATTR_CLASS => 'filter-form',
    ];

    protected const ATTRIBUTES_SEARCH = [
        Html5::ATTR_TYPE => Action::TYPE_SUBMIT,
    ];

    protected const ATTRIBUTES_RESET = [
        Html5::ATTR_TYPE => Action::TYPE_SUBMIT,
    ];

    /**
     * %1$s - hider button
     * %2$s - nodes (filters)
     */
    protected const DEFAULT_TEMPLATE = <<<'EOT'
        <div class="hidable">
            <p class="hider">%1$s</p>
            <div class="hidee">%2$s</div>
        </div>
        EOT;

    protected string $template = self::DEFAULT_TEMPLATE;

    /** @var IFilter[] */
    protected array $nodes = [];

    protected string $nodeClass = IFilter::class;

    protected Action $hiderBtn;
    protected Action $filterBtn;
    protected Action $resetBtn;

    protected Attributes $formAttributes;
    protected Attributes $searchAttributes;
    protected Attributes $resetAttributes;

    /**
     * Filters constructor.
     *
     * @param string[]        $intents
     * @param Attributes|null $attributes
     * @param string|null     $tag
     */
    public function __construct(array $intents = [], ?Attributes $attributes = null, ?string $tag = null)
    {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->formAttributes = new Attributes(static::ATTRIBUTES_FORM);
        $this->searchAttributes = new Attributes(static::ATTRIBUTES_SEARCH);
        $this->resetAttributes = new Attributes(static::ATTRIBUTES_RESET);

        $this->hiderBtn  = new Action(static::BTN_CONTENT_FILTERS, [Action::INTENT_INFO]);
        $this->filterBtn = new Action(static::BTN_CONTENT_FILTER, [Action::INTENT_PRIMARY], $this->searchAttributes);
        $this->resetBtn  = new Action(static::BTN_CONTENT_RESET, [Action::INTENT_SECONDARY], $this->resetAttributes);
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params): Filters
    {
        foreach ($this->nodes as $filter) {
            $filter->setParams($params);
        }

        return $this;
    }

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    public function getUrl(string $baseUrl): string
    {
        $queryParts = [];
        foreach ($this->nodes as $filter) {
            $queryPart = $filter->getQueryPart();
            if (!$queryPart) {
                continue;
            }

            $queryParts[] = $queryPart;
        }

        if (empty($queryParts)) {
            return $baseUrl;
        }

        return sprintf('%s%s&', $baseUrl, implode('&', $queryParts));
    }

    /**
     * @return array
     */
    public function getWhereConditions(): array
    {
        $conditions = [];
        foreach ($this->nodes as $filter) {
            $conditions = array_merge($conditions, $filter->getWhereConditions());
        }

        return $conditions;
    }

    /**
     * @return array<string,string>
     */
    public function getSqlParams(): array
    {
        $params = [];
        foreach ($this->nodes as $filter) {
            $params = array_merge($params, $filter->getQueryParams());
        }

        return $params;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return array_merge([$this->hiderBtn, $this->filterBtn, $this->resetBtn], $this->getNodes());
    }

    /**
     * @param string $template
     *
     * @return INode
     */
    public function setTemplate(string $template): INode
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $nodes = Collection::__toString();

        $form = TagHelper::toString($this->tag, $nodes, $this->formAttributes);

        return sprintf(
            $this->template,
            (string)$this->hiderBtn,
            $form
        );
    }

    /**
     * @return Action
     */
    public function getHiderBtn(): Action
    {
        return $this->hiderBtn;
    }

    /**
     * @return Action
     */
    public function getFilterBtn(): Action
    {
        return $this->filterBtn;
    }

    /**
     * @return Action
     */
    public function getResetBtn(): Action
    {
        return $this->resetBtn;
    }
}
