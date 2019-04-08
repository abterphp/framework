<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Cell\Sortable;
use AbterPhp\Framework\Grid\Row\IRow;
use AbterPhp\Framework\Html\Component;

class Header extends Component
{
    const DEFAULT_TAG = Html5::TAG_THEAD;

    /** @var IRow[] */
    protected $nodes = [];

    /** @var string */
    protected $nodeClass = IRow::class;

    /**
     * Header constructor.
     *
     * @param string[] $intents
     * @param array    $attributes
     */
    public function __construct(array $intents = [], array $attributes = [])
    {
        parent::__construct(null, $intents, $attributes);
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): Header
    {
        foreach ($this->nodes as $row) {
            foreach ($row->getCells() as $cell) {
                if (!($cell instanceof Sortable)) {
                    continue;
                }

                $cell->setBaseUrl($baseUrl);
            }
        }

        return $this;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params): Header
    {
        foreach ($this->nodes as $row) {
            foreach ($row->getCells() as $cell) {
                if (!($cell instanceof Sortable)) {
                    continue;
                }

                $cell->setParams($params);
            }
        }

        return $this;
    }

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    public function getSortedUrl(string $baseUrl): string
    {
        $params = [];
        foreach ($this->nodes as $row) {
            foreach ($row->getCells() as $cell) {
                if (!($cell instanceof Sortable)) {
                    continue;
                }

                $queryParam = $cell->getQueryParam();
                if (!$queryParam) {
                    continue;
                }

                $params[] = $queryParam;
            }
        }

        return $baseUrl . implode('', $params);
    }

    /**
     * @return array
     */
    public function getSortConditions(): array
    {
        $conditions = [];
        foreach ($this->nodes as $row) {
            foreach ($row->getCells() as $cell) {
                if (!($cell instanceof Sortable)) {
                    continue;
                }

                $conditions = array_merge($conditions, $cell->getSortConditions());
            }
        }

        return $conditions;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return [];
    }
}
