<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Domain\Entities;

use Opulence\Orm\IEntity;

interface IStringerEntity extends IEntity
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @return string
     */
    public function toJSON(): string;
}
