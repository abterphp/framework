<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

class ExactFilter extends Filter
{
    public const HELP_CONTENT = 'framework:helpExact';

    /**
     * @param array<string,string> $params
     *
     * @return $this
     */
    public function setParams(array $params): IFilter
    {
        parent::setParams($params);

        if ($this->getValue()) {
            $this->queryParams = [$this->getValue()];
        }

        return $this;
    }
}
