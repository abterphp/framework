<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use AbterPhp\Framework\Html\INodeContainer;
use AbterPhp\Framework\Html\ITag;

interface IPagination extends ITag, INodeContainer
{
    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @param int $totalCount
     *
     * @return IPagination
     */
    public function setTotalCount(int $totalCount): IPagination;

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    public function getPageSizeUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    public function setSortedUrl(string $baseUrl): IPagination;
}
