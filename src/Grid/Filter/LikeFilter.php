<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

class LikeFilter extends Filter
{
    const HELP_CONTENT = 'framework:helpLike';

    const QUERY_TEMPLATE = '%s LIKE ?';

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params): IFilter
    {
        parent::setParams($params);

        if ($this->value) {
            $this->queryParams = [$this->value];
        }

        return $this;
    }
}
