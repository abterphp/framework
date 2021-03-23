<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class EntityChange
{
    private IStringerEntity $entity;

    private string $eventType;

    /**
     * EntityCreated constructor.
     *
     * @param IStringerEntity $entity
     * @param string          $eventType
     */
    public function __construct(IStringerEntity $entity, string $eventType)
    {
        $this->entity    = $entity;
        $this->eventType = $eventType;
    }

    /**
     * @return IStringerEntity
     */
    public function getEntity(): IStringerEntity
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entity->getId();
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return get_class($this->entity);
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }
}
