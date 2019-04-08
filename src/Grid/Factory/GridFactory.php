<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Grid;
use AbterPhp\Framework\Grid\Pagination\IPagination;
use AbterPhp\Framework\Grid\Table\Table;

class GridFactory
{
    const ATTRIBUTE_CLASS = 'class';

    /** @var array */
    protected $attributes = [
        self::ATTRIBUTE_CLASS => 'grid',
    ];


    /**
     * @param TableFactory $table
     * @param IPagination  $pagination
     * @param Filters      $filters
     * @param Actions|null $actions
     *
     * @return Grid
     */
    public function create(Table $table, IPagination $pagination, Filters $filters, ?Actions $actions): Grid
    {
        return new Grid($table, $pagination, $filters, $actions, [], $this->attributes);
    }
}
