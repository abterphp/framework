<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use AbterPhp\Framework\Html\Contentless;

class Options extends Contentless
{
    protected int $defaultPageSize;

    /** @var int[] */
    protected array $pageSizeOptions;

    protected int $numberCount;

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

        parent::__construct();
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
}
