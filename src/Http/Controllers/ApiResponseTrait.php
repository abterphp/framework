<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

trait ApiResponseTrait
{
    /**
     * @param IStringerEntity $entity
     *
     * @return Response
     */
    protected function handleGetSuccess(IStringerEntity $entity): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_OK);
        $response->setContent($entity->toJSON());

        return $response;
    }

    /**
     * @param array $entities
     * @param int   $total
     *
     * @return Response
     */
    protected function handleListSuccess(array $entities, int $total): Response
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->toJSON();
        }
        $content = sprintf('{"total":%d,"data":[%s]}', $total, implode(',', $data));

        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_OK);
        $response->setContent($content);

        return $response;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @return Response
     */
    protected function handleCreateSuccess(IStringerEntity $entity): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_CREATED);
        $response->setContent($entity->toJSON());

        return $response;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @return Response
     */
    protected function handleUpdateSuccess(IStringerEntity $entity): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_OK);
        $response->setContent($entity->toJSON());

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleDeleteSuccess(): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_NO_CONTENT);

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleNotFound(): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_FOUND);

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleUnauthorized(): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_UNAUTHORIZED);

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleNotImplemented(): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_IMPLEMENTED);

        return $response;
    }
}
