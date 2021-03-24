<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Form\Element\Select;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\Html\NodeContainerTrait;
use AbterPhp\Framework\Html\Tag;

class Pagination extends Tag implements IPagination, ITemplater
{
    use NodeContainerTrait;

    protected const DEFAULT_TAG = Html5::TAG_DIV;

    /**
     * %1$s - numbers
     * %2$s - options
     */
    protected const DEFAULT_TEMPLATE = '<div class="gp-numbers">%1$s</div><div class="gp-options">%2$s%3$s</div>';

    public const PARAM_KEY_PAGE = 'page';
    public const PARAM_KEY_SIZE = 'page-size';

    protected const ERROR_MSG_INVALID_NUMBER_COUNT            = 'Number count must be a positive odd number.';
    protected const ERROR_MSG_INVALID_PAGE_SIZE               = 'Page size given is not allowed.';
    protected const ERROR_MSG_TOTAL_COUNT_NON_POSITIVE        = 'Total count must be a positive number.';
    protected const ERROR_MSG_TOTAL_COUNT_SMALLER_THAN_OFFSET = 'Offset must be smaller than total count.';

    public const LABEL_CONTENT = 'framework:pageSize';

    /** @var array */
    protected array $params = [];

    /** @var int */
    protected int $rangeStart = 0;

    /** @var int */
    protected int $rangeEnd = 0;

    /** @var int */
    protected int $pageSize = 0;

    /** @var int */
    protected int $totalCount = 0;

    /** @var int */
    protected int $numberCount = 5;

    /** @var array */
    protected array $attributes = [];

    /** @var Numbers */
    protected Numbers $numbers;

    /** @var Select */
    protected Select $sizeOptions;

    /** @var string */
    protected string $template = self::DEFAULT_TEMPLATE;

    /**
     * Pagination constructor.
     *
     * @param array       $params
     * @param string      $baseUrl
     * @param int         $numberCount
     * @param int         $pageSize
     * @param array       $pageSizes
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(
        array $params,
        string $baseUrl,
        int $numberCount,
        int $pageSize,
        array $pageSizes,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        $this->params      = $params;
        $this->pageSize    = $pageSize;
        $this->numberCount = $numberCount;

        $this->setParams($params);

        $this->checkArguments($pageSizes);

        $this->buildComponents($baseUrl, $pageSizes);

        parent::__construct(null, $intents, $attributes, $tag);

        $this->appendToAttribute(Html5::ATTR_CLASS, 'grid-pagination row');
    }

    /**
     * @param array $params
     */
    protected function setParams(array $params): void
    {
        $page = 1;
        if (array_key_exists(static::PARAM_KEY_PAGE, $params)) {
            $page = $params[static::PARAM_KEY_PAGE];
        }

        if (array_key_exists(static::PARAM_KEY_SIZE, $params)) {
            $this->pageSize = (int)$params[static::PARAM_KEY_SIZE];
        }

        $this->rangeStart = ($page - 1) * $this->pageSize;
    }

    /**
     * @param array $pageSizes
     */
    protected function checkArguments(array $pageSizes): void
    {
        if ($this->numberCount % 2 !== 1 || $this->numberCount < 1) {
            throw new \InvalidArgumentException(static::ERROR_MSG_INVALID_NUMBER_COUNT);
        }
        if (!in_array($this->pageSize, $pageSizes, true)) {
            throw new \InvalidArgumentException(static::ERROR_MSG_INVALID_PAGE_SIZE);
        }
    }

    /**
     * @param string $baseUrl
     * @param array  $pageSizes
     */
    protected function buildComponents(string $baseUrl, array $pageSizes): void
    {
        $baseUrl    = $this->getPageSizeUrl($baseUrl);
        $attributes = [
            Html5::ATTR_CLASS => ['pagination-sizes'],
        ];

        $this->numbers     = new Numbers($baseUrl);
        $this->sizeOptions = new Select(
            'pagination-sizes',
            'pagination-sizes',
            [],
            $attributes
        );

        foreach ($pageSizes as $pageSize) {
            $isSelected = ($pageSize === $this->pageSize);
            $option     = new Option((string)$pageSize, (string)$pageSize, $isSelected);

            $this->sizeOptions[] = $option;
        }
    }

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    public function getPageSizeUrl(string $baseUrl): string
    {
        return $baseUrl . sprintf('%s=%d&', static::PARAM_KEY_SIZE, $this->pageSize);
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setSortedUrl(string $baseUrl): IPagination
    {
        $this->numbers->setBaseUrl($baseUrl);

        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount(int $totalCount): IPagination
    {
        if ($totalCount < 0) {
            throw new \InvalidArgumentException(static::ERROR_MSG_TOTAL_COUNT_NON_POSITIVE);
        }
        if ($this->rangeStart && $this->rangeStart > $totalCount) {
            throw new \InvalidArgumentException(static::ERROR_MSG_TOTAL_COUNT_SMALLER_THAN_OFFSET);
        }

        $this->totalCount = $totalCount;
        $this->rangeEnd   = min($this->totalCount, $this->rangeStart + $this->pageSize - 1);

        $currentPage = $this->getCurrentPage();
        $pageNumbers = $this->getPageNumbers($currentPage);
        $lastPage    = (int)ceil($totalCount / $this->pageSize);

        $this->numbers->populate($currentPage, $pageNumbers, $lastPage);

        return $this;
    }

    /**
     * @return int
     */
    protected function getCurrentPage(): int
    {
        $currentPage = (int)floor($this->rangeStart / $this->pageSize) + 1;

        return $currentPage;
    }

    /**
     * @return int
     */
    protected function getMinPageNumber(int $currentPage): int
    {
        $minPage = (int)($currentPage - floor($this->numberCount / 2));
        $result  = (int)max($minPage, 1);

        return $result;
    }

    /**
     * @return int
     */
    protected function getMaxPageNumber(int $currentPage): int
    {
        $maxPage = (int)($currentPage + floor($this->numberCount / 2));
        $result  = (int)min($maxPage, max(ceil($this->totalCount / $this->pageSize), 1));

        return $result;
    }

    /**
     * @param int $currentPage
     *
     * @return int[]
     */
    protected function getPageNumbers(int $currentPage): array
    {
        $minPageNumber = $this->getMinPageNumber($currentPage);
        $maxPageNumber = $this->getMaxPageNumber($currentPage);

        $numbers = [];
        for ($i = 0; ($i < $this->numberCount) && ($minPageNumber + $i <= $maxPageNumber); $i++) {
            $numbers[] = $minPageNumber + $i;
        }

        return $numbers;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return array_merge([$this->numbers, $this->sizeOptions], $this->getNodes());
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        return [];
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
     * @return string
     */
    public function __toString(): string
    {
        $numbers     = (string)$this->numbers;
        $sizeLabel   = $this->translator ? $this->translator->translate(static::LABEL_CONTENT) : static::LABEL_CONTENT;
        $sizeOptions = (string)$this->sizeOptions;

        $content = sprintf($this->template, $numbers, $sizeLabel, $sizeOptions);

        return StringHelper::wrapInTag($content, $this->tag, $this->attributes);
    }
}
