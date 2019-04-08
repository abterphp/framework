<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Action;

use AbterPhp\Framework\Html\IComponent;
use Opulence\Orm\IEntity;

interface IAction extends IComponent
{
    public function setEntity(IEntity $entity);

    public function duplicate(): IAction;
}
