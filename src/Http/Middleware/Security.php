<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Security\SecurityException;
use Closure;
use Opulence\Cache\ICacheBridge;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;

class Security implements IMiddleware
{
    const KEY = 'abterphp:security';

    /** @var ICacheBridge */
    protected $cacheBridge;

    /**
     * Security constructor.
     *
     * @param ICacheBridge $cacheBridge
     */
    public function __construct(ICacheBridge $cacheBridge)
    {
        $this->cacheBridge = $cacheBridge;
    }

    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        if (getenv(\AbterPhp\Framework\Constant\Env::ENV_NAME) !== Environment::PRODUCTION) {
            return $next($request);
        }

        // phpcs:disable Generic.CodeAnalysis.EmptyStatement
        try {
            if ($this->cacheBridge->get(static::KEY)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // It's always safe to check potential security risks, only makes the response slightly slower
        }
        // phpcs:enable Generic.CodeAnalysis.EmptyStatement

        $this->checkSecrets();
        $this->checkPhpSettings();

        $this->cacheBridge->set(static::KEY, true, PHP_INT_MAX);

        return $next($request);
    }

    private function checkSecrets()
    {
        if (getenv(Env::DB_PASSWORD) === '28T3pqyvKG3tEgsjE8Rj') {
            throw new SecurityException('Invalid DB_PASSWORD environment variable.');
        }

        if (getenv(Env::ENCRYPTION_KEY) === 'b8fbb40c129ad0e426e19b7d28f42e517ce639282e55ba7a98bd2b698fda7daa') {
            throw new SecurityException('Invalid ENCRYPTION_KEY environment variable.');
        }

        if (getenv(Env::CRYPTO_FRONTEND_SALT) === 'R6n9gNH9ND6USc6D') {
            throw new SecurityException('Invalid CRYPTO_FRONTEND_SALT environment variable.');
        }

        if (getenv(Env::CRYPTO_ENCRYPTION_PEPPER) === 'h9fyyWr36vBnky9G') {
            throw new SecurityException('Invalid CRYPTO_ENCRYPTION_PEPPER environment variable.');
        }
    }

    private function checkPhpSettings()
    {
        if (ini_get('display_errors')) {
            throw new SecurityException('Unacceptable `display_errors` value for production.');
        }
    }
}
