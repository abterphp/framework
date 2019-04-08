<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Service\Execute;

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
     * @param string[]       $postData
     * @param UploadedFile[] $fileData
     *
     * @return string id of the created entity
     */
    public function create(array $postData, array $fileData): string;

    /**
     * @param string         $entityId
     * @param string[]       $postData
     * @param UploadedFile[] $fileData
     *
     * @return bool
     */
    public function update(string $entityId, array $postData, array $fileData): bool;

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function delete(string $entityId): bool;
}
