<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\IAction;
use AbterPhp\Framework\Html\Component;

class Actions extends Component
{
    const DEFAULT_TAG = Html5::TAG_DIV;

    /** @var IAction[] */
    protected $nodes = [];

    /** @var string */
    protected $nodeClass = IAction::class;

    /**
     * @return Actions
     */
    public function duplicate(): Actions
    {
        $actionsCopy = new Actions();

        foreach ($this->nodes as $action) {
            $actionCopy    = $action->duplicate();
            $actionsCopy[] = $actionCopy;
        }

        return $actionsCopy;
    }
}
