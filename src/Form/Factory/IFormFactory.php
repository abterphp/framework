<?php

namespace AbterPhp\Framework\Form\Factory;

use AbterPhp\Framework\Form\IForm;
use Opulence\Orm\IEntity;

interface IFormFactory
{
    const ERR_MSG_ENTITY_MISSING = 'Entity missing';

    /**
     * @param string       $action
     * @param string       $method
     * @param string       $showUrl
     * @param IEntity|null $entity
     *
     * @return $this
     */
    public function create(string $action, string $method, string $showUrl, ?IEntity $entity = null): IForm;
}
