<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

interface IBuilder
{
    /**
     * @param                     $data
     * @param ParsedTemplate|null $template
     *
     * @return IData
     */
    public function build($data, ?ParsedTemplate $template = null): IData;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
