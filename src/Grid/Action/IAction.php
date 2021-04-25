<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Action;

use AbterPhp\Framework\Html\ITag;
use Opulence\Orm\IEntity;

interface IAction extends ITag
{
    public function setEntity(IEntity $entity);
}
