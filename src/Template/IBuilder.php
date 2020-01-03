<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

interface IBuilder
{
    /**
     * @param mixed $data
     *
     * @return IData
     */
    public function build($data): IData;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
