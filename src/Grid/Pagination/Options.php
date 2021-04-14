<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use AbterPhp\Framework\Html\Attributes;

class Options
{
    protected int $defaultPageSize;

    /** @var int[] */
    protected array $pageSizeOptions;

    protected int $numberCount;

    protected Attributes $attributes;

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

        $this->attributes = new Attributes();
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
     * @return Attributes
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * @param Attributes $attributes
     *
     * @return $this
     */
    public function setAttributes(Attributes $attributes): Options
    {
        $this->attributes = $attributes;

        return $this;
    }
}
