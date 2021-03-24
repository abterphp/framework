<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use Exception;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Sessions\Http\Middleware\Session as BaseSession;
use Opulence\Http\Responses\Cookie;
use Opulence\Http\Responses\Response;

/**
 * Defines the session middleware
 */
class Session extends BaseSession
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * Runs garbage collection, if necessary
     *
     * @throws Exception
     */
    protected function gc(): void
    {
        if (random_int(1, Config::get('sessions', 'gc.divisor')) <= Config::get('sessions', 'gc.chance')) {
            $this->sessionHandler->gc(Config::get('sessions', 'lifetime'));
        }
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * Writes any session data needed in the response
     *
     * @param Response $response The response to write to
     *
     * @return Response The response with data written to it
     */
    protected function writeToResponse(Response $response): Response
    {
        $response->getHeaders()->setCookie(
            new Cookie(
                $this->session->getName(),
                $this->session->getId(),
                time() + Config::get('sessions', 'lifetime'),
                Config::get('sessions', 'cookie.path'),
                Config::get('sessions', 'cookie.domain'),
                Config::get('sessions', 'cookie.isSecure'),
                Config::get('sessions', 'cookie.isHttpOnly')
            )
        );

        return $response;
    }
}
