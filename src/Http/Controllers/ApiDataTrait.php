<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers;

trait ApiDataTrait
{
    /** @var string */
    protected $problemBaseUrl;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array $data
     *
     * @return UploadedFile[]
     */
    protected function getFileData(array $data): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCreateData(): array
    {
        return $this->getSharedData();
    }

    /**
     * @return array
     */
    public function getUpdateData(): array
    {
        return $this->getSharedData();
    }

    /**
     * @return array
     */
    public function getSharedData(): array
    {
        return $this->request->getJsonBody();
    }
}
