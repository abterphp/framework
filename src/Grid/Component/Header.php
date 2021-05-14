<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Cell\Sortable;
use AbterPhp\Framework\Grid\Row\IRow;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Tag;

class Header extends Tag
{
    protected const DEFAULT_TAG  = Html5::TAG_THEAD;
    protected const CONTENT_TYPE = IRow::class;

    /** @var IRow[] */
    protected array $content = [];

    protected string $nodeClass = IRow::class;

    /**
     * Header constructor.
     *
     * @param IRow[]|null                  $content
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     */
    public function __construct(?array $content = null, array $intents = [], ?array $attributes = null)
    {
        parent::__construct($content, $intents, $attributes);
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): Header
    {
        foreach ($this->content as $row) {
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
        foreach ($this->content as $row) {
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
        foreach ($this->content as $row) {
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
     * @return array<string,string>
     */
    public function getSortConditions(): array
    {
        $conditions = [];
        foreach ($this->content as $row) {
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
     * @return array<string,string>
     */
    public function getQueryParams(): array
    {
        return [];
    }
}
