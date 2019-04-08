<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

class Options
{
    /** @var int */
    protected $defaultPageSize;

    /** @var int[] */
    protected $pageSizeOptions;

    /** @var int */
    protected $numberCount;

    /** @var array */
    protected $attributes = [];

    /**
     * Options constructor.
     *
     * @param int   $defaultPageSize
     * @param array $pageSizeOptions
     * @param int   $numberCount
     */
    public function __construct(int $defaultPageSize, array $pageSizeOptions, int $numberCount)
    {
        $this->defaultPageSize = $defaultPageSize;
        $this->pageSizeOptions = $pageSizeOptions;
        $this->numberCount     = $numberCount;
    }

    /**
     * @return int
     */
    public function getDefaultPageSize(): int
    {
        return $this->defaultPageSize;
    }

    /**
     * @return int[]
     */
    public function getPageSizeOptions(): array
    {
        return $this->pageSizeOptions;
    }

    /**
     * @return int
     */
    public function getNumberCount(): int
    {
        return $this->numberCount;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): Options
    {
        $this->attributes = $attributes;

        return $this;
    }
}
