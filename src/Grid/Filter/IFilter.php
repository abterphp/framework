<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\IComponent;

interface IFilter extends IComponent
{
    /**
     * @param array<string,string> $params
     *
     * @return IFilter
     */
    public function setParams(array $params): IFilter;

    /**
     * @return string[]
     */
    public function getWhereConditions(): array;

    /**
     * @return array<string,string>
     */
    public function getQueryParams(): array;

    /**
     * @return string
     */
    public function getQueryPart(): string;

    /**
     * @return IComponent
     */
    public function getWrapper(): IComponent;

    /**
     * @return Label|null
     */
    public function getLabel(): ?Label;
}
