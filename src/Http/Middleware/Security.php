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

    const TEST_DB_PASSWORD              = '28T3pqyvKG3tEgsjE8Rj';
    const TEST_ENCRYPTION_KEY           = 'b8fbb40c129ad0e426e19b7d28f42e517ce639282e55ba7a98bd2b698fda7daa';
    const TEST_CRYPTO_FRONTEND_SALT     = 'R6n9gNH9ND6USc6D';
    const TEST_CRYPTO_ENCRYPTION_PEPPER = 'h9fyyWr36vBnky9G';

    /** @var ICacheBridge */
    protected $cacheBridge;

    /** @var string */
    protected $environment;

    /** @var string[] */
    protected $environmentData;

    /** @var string[] */
    protected $settings;

    /**
     * Security constructor.
     *
     * @param ICacheBridge $cacheBridge
     */
    public function __construct(ICacheBridge $cacheBridge)
    {
        $this->cacheBridge = $cacheBridge;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        if (null === $this->environment) {
            $this->environment = getenv(Env::ENV_NAME);
        }

        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getEnvironmentData(string $key): string
    {
        if (!isset($this->environmentData[$key])) {
            return (string)getenv($key);
        }

        return (string)$this->environmentData[$key];
    }

    /**
     * @param array $environmentData
     *
     * @return $this
     */
    public function setEnvironmentData(array $environmentData): Security
    {
        $this->environmentData = $environmentData;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getSetting(string $key): string
    {
        if (!isset($this->settings[$key])) {
            return (string)getenv($key);
        }

        return (string)$this->settings[$key];
    }

    /**
     * @param array $settings
     *
     * @return Security
     */
    public function setSettings(array $settings): Security
    {
        $this->settings = $settings;

        return $this;
    }

    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->getEnvironment() !== Environment::PRODUCTION) {
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
        if ($this->getEnvironmentData(Env::DB_PASSWORD) === static::TEST_DB_PASSWORD) {
            throw new SecurityException('Invalid DB_PASSWORD environment variable.');
        }

        if ($this->getEnvironmentData(Env::ENCRYPTION_KEY) === static::TEST_ENCRYPTION_KEY) {
            throw new SecurityException('Invalid ENCRYPTION_KEY environment variable.');
        }

        if ($this->getEnvironmentData(Env::CRYPTO_FRONTEND_SALT) === static::TEST_CRYPTO_FRONTEND_SALT) {
            throw new SecurityException('Invalid CRYPTO_FRONTEND_SALT environment variable.');
        }

        if ($this->getEnvironmentData(Env::CRYPTO_ENCRYPTION_PEPPER) === static::TEST_CRYPTO_ENCRYPTION_PEPPER) {
            throw new SecurityException('Invalid CRYPTO_ENCRYPTION_PEPPER environment variable.');
        }
    }

    private function checkPhpSettings()
    {
        if ($this->getSetting('display_errors')) {
            throw new SecurityException('Unacceptable `display_errors` value for production.');
        }
    }
}
