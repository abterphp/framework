<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Pagination\Options;
use AbterPhp\Framework\Grid\Pagination\Pagination;

class PaginationFactory
{
    /** @var Options */
    protected $options;

    /** @var int */
    protected $pageSize;

    /**
     * Pagination constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;

        $this->pageSize = $options->getDefaultPageSize();
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize(int $pageSize): PaginationFactory
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @param array  $params
     * @param string $baseUrl
     *
     * @return Pagination
     */
    public function create(array $params, string $baseUrl): Pagination
    {
        return new Pagination(
            $params,
            $baseUrl,
            $this->options->getNumberCount(),
            $this->options->getDefaultPageSize(),
            $this->options->getPageSizeOptions(),
            [],
            $this->options->getAttributes()
        );
    }
}
