<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\Config\Config;
use Exception;
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
        $rand   = random_int(1, Config::mustGetInt('sessions', 'gc.divisor'));
        $chance = Config::mustGetInt('sessions', 'gc.chance');
        if ($rand <= $chance) {
            $this->sessionHandler->gc(Config::mustGetInt('sessions', 'lifetime'));
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
                time() + Config::mustGetInt('sessions', 'lifetime'),
                Config::mustGetString('sessions', 'cookie.path'),
                Config::mustGetString('sessions', 'cookie.domain'),
                Config::mustGetBool('sessions', 'cookie.isSecure'),
                Config::mustGetBool('sessions', 'cookie.isHttpOnly')
            )
        );

        return $response;
    }
}
