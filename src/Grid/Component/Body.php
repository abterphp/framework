<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Row\IRow;
use AbterPhp\Framework\Grid\Row\Row;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Body extends Tag
{
    protected const DEFAULT_TAG  = Html5::TAG_TBODY;
    protected const CONTENT_TYPE = IRow::class;

    /** @var array<string,callable> */
    protected array $getters;

    protected ?Actions $actions;

    /** @var IRow[] */
    protected array $content = [];

    /**
     * Body constructor.
     *
     * @param array<string,callable>  $getters
     * @param Actions|null            $actions
     * @param IRow[]|null             $content
     * @param string[]                $intents
     * @param array<string,Attribute> $attributes
     */
    public function __construct(
        array $getters,
        ?Actions $actions,
        ?array $content = null,
        array $intents = [],
        array $attributes = []
    ) {
        parent::__construct($content, $intents, $attributes);

        $this->getters = $getters;
        $this->actions = $actions;
    }

    /**
     * @param IStringerEntity[] $entities
     *
     * @return Body
     */
    public function setEntities(array $entities): Body
    {
        foreach ($entities as $entity) {
            $cells = $this->createCells($entity);

            $actions = $this->actions ? clone $this->actions : null;

            $row = new Row($cells, $actions);
            $row->setEntity($entity);

            $this->content[] = $row;
        }

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = [];
        if ($this->actions) {
            $nodes[] = $this->actions;
        }

        return array_merge($nodes, $this->getNodes());
    }

    /**
     * @param IStringerEntity $entity
     *
     * @return Cells
     */
    private function createCells(IStringerEntity $entity): Cells
    {
        $cells = new Cells();
        foreach ($this->getters as $group => $getter) {
            $content = is_callable($getter) ? $getter($entity) : (string)$entity->$getter();

            $cells[] = new Cell($content, $group, [], $this->attributes, Html5::TAG_TD);
        }

        return $cells;
    }
}
