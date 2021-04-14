<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Row;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\IAction;  // @phan-suppress-current-line PhanUnreferencedUseNormal
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Helper\TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\NodeContainerTrait;
use AbterPhp\Framework\Html\Tag;
use Opulence\Orm\IEntity;

class Row extends Tag implements IRow
{
    use NodeContainerTrait;

    protected const DEFAULT_TAG = Html5::TAG_TR;

    /** @var Cells */
    protected Cells $cells;

    /** @var Actions */
    protected Actions $actions;

    /** @var Cell */
    protected Cell $actionCell;

    /** @var IEntity */
    protected IEntity $entity;

    /**
     * Row constructor.
     *
     * @param Cells        $cells
     * @param Actions|null $actions
     * @param string[]     $intents
     * @param Attributes|null $attributes
     * @param string|null  $tag
     */
    public function __construct(
        Cells $cells,
        ?Actions $actions = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct(null, $intents, $attributes, $tag);

        $this->cells   = $cells;
        $this->actions = $actions ?? new Actions();

        if ($actions) {
            $this->actionCell = new Cell($this->actions, Cell::GROUP_ACTIONS, [Cell::INTENT_ACTIONS]);
        }
    }

    /**
     * @return Cells
     */
    public function getCells(): Cells
    {
        return $this->cells;
    }

    /**
     * @return IEntity
     */
    public function getEntity(): IEntity
    {
        return $this->entity;
    }

    /**
     * @param IEntity $entity
     */
    public function setEntity(IEntity $entity): void
    {
        $this->entity = $entity;

        if (null === $this->actions) {
            return;
        }

        foreach ($this->actions as $action) {
            if ($action instanceof IAction) {
                $action->setEntity($entity);
            }
        }
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = [$this->cells];
        if ($this->actionCell) {
            $nodes[] = $this->actionCell;
        }

        return array_merge($nodes, $this->getNodes());
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = (string)$this->cells;

        if ($this->actionCell) {
            $content .= $this->actionCell;
        }

        return TagHelper::toString($this->tag, $content, $this->attributes);
    }
}
