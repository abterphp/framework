<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory\Table;

use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Body;

class BodyFactory
{
    /**
     * @param array        $getters
     * @param array        $rowArguments
     * @param Actions|null $rowActions
     *
     * @return Body
     */
    public function create(
        array $getters,
        array $rowArguments,
        ?Actions $rowActions
    ): Body {
        $body = new Body($getters, $rowArguments, $rowActions);

        return $body;
    }
}
