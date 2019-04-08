<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Filter\IFilter;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;

class Filters extends Component implements ITemplater
{
    const DEFAULT_TAG = Html5::TAG_FORM;

    const BTN_CONTENT_FILTERS = 'framework:filters';
    const BTN_CONTENT_FILTER  = 'framework:filter';
    const BTN_CONTENT_RESET   = 'framework:reset';

    const ATTRIBUTES_FORM = [
        Html5::ATTR_CLASS => 'filter-form',
    ];

    const ATTRIBUTES_SEARCH = [
        Html5::ATTR_TYPE => Action::TYPE_SUBMIT,
    ];

    const ATTRIBUTES_RESET = [
        Html5::ATTR_TYPE => Action::TYPE_SUBMIT,
    ];

    /**
     * %1$s - hider button
     * %2$s - nodes (filters)
     */
    const DEFAULT_TEMPLATE = <<<'EOT'
<div class="hidable">
    <p class="hider">%1$s</p>
    <div class="hidee">%2$s</div>
</div>
EOT;

    protected $template = self::DEFAULT_TEMPLATE;

    /** @var IFilter[] */
    protected $nodes = [];

    /** @var string */
    protected $nodeClass = IFilter::class;

    /** @var Action */
    protected $hiderBtn;

    /** @var Action */
    protected $filterBtn;

    /** @var Action */
    protected $resetBtn;

    /**
     * Filters constructor.
     *
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(array $intents = [], array $attributes = [], ?string $tag = null)
    {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->hiderBtn  = new Action(static::BTN_CONTENT_FILTERS, [Action::INTENT_INFO]);
        $this->filterBtn = new Action(static::BTN_CONTENT_FILTER, [Action::INTENT_PRIMARY], static::ATTRIBUTES_SEARCH);
        $this->resetBtn  = new Action(static::BTN_CONTENT_RESET, [Action::INTENT_SECONDARY], static::ATTRIBUTES_RESET);
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
     * @return array
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

        $form = StringHelper::wrapInTag(
            $nodes,
            $this->tag,
            static::ATTRIBUTES_FORM
        );

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
