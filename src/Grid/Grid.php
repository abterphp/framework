<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Pagination\IPagination;
use AbterPhp\Framework\Grid\Table\ITable;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;

class Grid extends Component implements IGrid, ITemplater
{
    /**
     * %1$s - filter
     * %2$s - actions
     * %3$s - table
     * %4$s - pagination
     */
    protected const DEFAULT_TEMPLATE = '%1$s%4$s%2$s%3$s%4$s';

    protected const TAG_GRID    = 'div';
    protected const TAG_FILTER  = 'div';
    protected const TAG_ACTIONS = 'div';

    protected const ATTRIBUTE_GRID_CLASS    = 'grid';
    protected const ATTRIBUTE_FILTER_CLASS  = 'grid-filters';
    protected const ATTRIBUTE_ACTIONS_CLASS = 'grid-actions';

    protected string $containerClass = '';

    protected ITable $table;

    protected ?IPagination $pagination = null;

    protected ?Filters $filters = null;

    protected ?Actions $actions = null;

    protected string $template = self::DEFAULT_TEMPLATE;

    /**
     * Grid constructor.
     *
     * @param ITable           $table
     * @param IPagination|null $pagination
     * @param Filters|null     $filters
     * @param Actions|null     $actions
     * @param string[]         $intents
     * @param Attributes|null  $attributes
     */
    public function __construct(
        ITable $table,
        IPagination $pagination = null,
        Filters $filters = null,
        Actions $actions = null,
        array $intents = [],
        ?Attributes $attributes = null
    ) {
        $this->table      = $table;
        $this->pagination = $pagination;

        parent::__construct(null, $intents, $attributes, static::TAG_GRID);

        $this->appendToClass(static::ATTRIBUTE_GRID_CLASS);

        if ($actions) {
            $this->actions = $actions;
            $this->actions->appendToClass(static::ATTRIBUTE_ACTIONS_CLASS);
        }

        if ($filters) {
            $this->filters = $filters;
            $this->filters->appendToClass(static::ATTRIBUTE_FILTER_CLASS);
        }
    }

    /**
     * @return Filters
     */
    public function getFilters(): Filters
    {
        if ($this->filters instanceof Filters) {
            return $this->filters;
        }

        $filters = new Filters();

        $this->filters = $filters;

        return $filters;
    }

    /**
     * @return Actions
     */
    public function getActions(): Actions
    {
        if ($this->actions instanceof Actions) {
            return $this->actions;
        }

        $actions = new Actions();

        $this->actions = $actions;

        return $actions;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        if (!$this->pagination) {
            throw new \LogicException();
        }

        return $this->pagination->getPageSize();
    }

    /**
     * @return array
     */
    public function getSortConditions(): array
    {
        return $this->table->getSortConditions();
    }

    /**
     * @return array
     */
    public function getWhereConditions(): array
    {
        if (!$this->filters) {
            throw new \LogicException();
        }

        return $this->filters->getWhereConditions();
    }

    /**
     * @return array<string,string>
     */
    public function getSqlParams(): array
    {
        if (!$this->filters) {
            throw new \LogicException();
        }

        $tableParams   = $this->table->getSqlParams();
        $filtersParams = $this->filters->getSqlParams();

        return array_merge($tableParams, $filtersParams);
    }

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount(int $totalCount): IGrid
    {
        if (!$this->pagination) {
            throw new \LogicException();
        }

        $this->pagination->setTotalCount($totalCount);

        return $this;
    }

    /**
     * @param IStringerEntity[] $entities
     *
     * @return IGrid
     */
    public function setEntities(array $entities): IGrid
    {
        $this->table->setEntities($entities);

        return $this;
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
        $extendedNodes = [];
        if ($this->filters) {
            $extendedNodes[] = $this->filters;
        }
        if ($this->pagination) {
            $extendedNodes[] = $this->pagination;
        }
        if ($this->table) {
            $extendedNodes[] = $this->table;
        }

        return array_merge($extendedNodes, $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $filters    = (string)$this->getFilters();
        $actions    = (string)$this->getActions();
        $table      = (string)$this->table;
        $pagination = (string)$this->pagination;

        $content = sprintf($this->template, $filters, $actions, $table, $pagination);

        return TagHelper::toString($this->tag, $content, $this->attributes);
    }
}
