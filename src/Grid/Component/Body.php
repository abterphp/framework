<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Row\IRow;
use AbterPhp\Framework\Grid\Row\Row;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Html\INode;

class Body extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_TBODY;

    /** @var array */
    protected $getters;

    /** @var array */
    protected $rowArguments;

    /** @var Actions|null */
    protected $actions;

    /** @var IRow[] */
    protected $nodes = [];

    /** @var string */
    protected $nodeClass = IRow::class;

    /**
     * Body constructor.
     *
     * @param array        $getters
     * @param array        $rowArguments
     * @param Actions|null $actions
     */
    public function __construct(
        array $getters,
        array $rowArguments,
        ?Actions $actions
    ) {
        parent::__construct();

        $this->getters      = $getters;
        $this->rowArguments = $rowArguments;
        $this->actions      = $actions;
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

            $actions = $this->actions ? $this->actions->duplicate() : null;

            $row = new Row($cells, $actions, $this->rowArguments);
            $row->setEntity($entity);

            $this->nodes[] = $row;
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
