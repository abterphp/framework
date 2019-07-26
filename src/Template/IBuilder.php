<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

interface IBuilder
{
    /**
     * @param IStringerEntity[] $entities
     *
     * @return IData
     */
    public function build(array $entities): IData;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
