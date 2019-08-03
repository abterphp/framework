<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Service\Execute;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Http\Requests\UploadedFile;

interface IRepoService
{
    /**
     * @param array $postData
     *
     * @return array
     */
    public function validateForm(array $postData): array;

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     */
    public function retrieveEntity(string $entityId): IStringerEntity;

    /**
     * @param int      $offset
     * @param int      $limit
     * @param string[] $orders
     * @param array    $conditions
     * @param array    $params
     *
     * @return IStringerEntity[]
     */
    public function retrieveList(int $offset, int $limit, array $orders, array $conditions, array $params): array;

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     */
    public function createEntity(string $entityId): IStringerEntity;

    /**
     * @param string[]       $postData
     * @param UploadedFile[] $fileData
     *
     * @return IStringerEntity
     */
    public function create(array $postData, array $fileData): IStringerEntity;

    /**
     * @param IStringerEntity $entity
     * @param string[]        $postData
     * @param UploadedFile[]  $fileData
     *
     * @return bool
     */
    public function update(IStringerEntity $entity, array $postData, array $fileData): bool;

    /**
     * @param IStringerEntity $entity
     *
     * @return bool
     */
    public function delete(IStringerEntity $entity): bool;
}
