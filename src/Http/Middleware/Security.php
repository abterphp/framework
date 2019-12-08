<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Exception\Security as SecurityException;
use Closure;
use Opulence\Cache\ICacheBridge;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;

class Security implements IMiddleware
{
    protected const KEY = 'abterphp:security';

    public const TEST_DB_PASSWORD                 = '28T3pqyvKG3tEgsjE8Rj';
    public const TEST_ENCRYPTION_KEY              = 'b8fbb40c129ad0e426e19b7d28f42e517ce639282e55ba7a98bd2b698fda7daa';
    public const TEST_CRYPTO_FRONTEND_SALT        = 'R6n9gNH9ND6USc6D';
    public const TEST_CRYPTO_ENCRYPTION_PEPPER    = 'h9fyyWr36vBnky9G';
    public const TEST_OAUTH2_PRIVATE_KEY_PATH     = '/website/tests/resources/private.key';
    public const TEST_OAUTH2_PRIVATE_KEY_PASSWORD = '8a!J2SZ9%WBII#9Z';
    public const TEST_OAUTH2_PUBLIC_KEY_PATH      = '/website/tests/resources/public.key';
    public const TEST_OAUTH2_ENCRYPTION_KEY       = 'def00000cea4c75b84279f43b56dd90851609717c5d29c215fd2c67f9b1acb0c3b1c5ff8528dbeecf0c1f368baa33284aa36d00b24994872970933e8881802287553ff7d'; // phpcs:ignore

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
     * @param string       $environment
     */
    public function __construct(ICacheBridge $cacheBridge, string $environment)
    {
        $this->cacheBridge = $cacheBridge;
        $this->environment = $environment;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getVar(string $key): string
    {
        if (!isset($this->environmentData[$key])) {
            return (string)Environment::getVar($key);
        }

        return (string)$this->environmentData[$key];
    }

    /**
     * @param array $environmentData
     *
     * @return $this
     */
    public function setVar(array $environmentData): Security
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
            return (string)ini_get($key);
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
        if ($this->environment !== Environment::PRODUCTION) {
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

        $this->checkGeneralSecrets();
        $this->checkOauth2Secrets();
        $this->checkPhpSettings();

        $this->cacheBridge->set(static::KEY, true, PHP_INT_MAX);

        return $next($request);
    }

    private function checkGeneralSecrets()
    {
        if ($this->getVar(Env::DB_PASSWORD) === static::TEST_DB_PASSWORD) {
            throw new SecurityException('Invalid DB_PASSWORD environment variable.');
        }

        if ($this->getVar(Env::ENCRYPTION_KEY) === static::TEST_ENCRYPTION_KEY) {
            throw new SecurityException('Invalid ENCRYPTION_KEY environment variable.');
        }

        if ($this->getVar(Env::CRYPTO_FRONTEND_SALT) === static::TEST_CRYPTO_FRONTEND_SALT) {
            throw new SecurityException('Invalid CRYPTO_FRONTEND_SALT environment variable.');
        }

        if ($this->getVar(Env::CRYPTO_ENCRYPTION_PEPPER) === static::TEST_CRYPTO_ENCRYPTION_PEPPER) {
            throw new SecurityException('Invalid CRYPTO_ENCRYPTION_PEPPER environment variable.');
        }
    }

    private function checkOauth2Secrets()
    {
        if ($this->getVar(Env::OAUTH2_PRIVATE_KEY_PATH) === static::TEST_OAUTH2_PRIVATE_KEY_PATH) {
            throw new SecurityException('Invalid OAUTH2_PRIVATE_KEY_PATH environment variable.');
        }

        if ($this->getVar(Env::OAUTH2_PRIVATE_KEY_PASSWORD) === static::TEST_OAUTH2_PRIVATE_KEY_PASSWORD) {
            throw new SecurityException('Invalid OAUTH2_PRIVATE_KEY_PASSWORD environment variable.');
        }

        if ($this->getVar(Env::OAUTH2_PUBLIC_KEY_PATH) === static::TEST_OAUTH2_PUBLIC_KEY_PATH) {
            throw new SecurityException('Invalid OAUTH2_PUBLIC_KEY_PATH environment variable.');
        }

        if ($this->getVar(Env::OAUTH2_ENCRYPTION_KEY) === static::TEST_OAUTH2_ENCRYPTION_KEY) {
            throw new SecurityException('Invalid OAUTH2_ENCRYPTION_KEY environment variable.');
        }
    }

    private function checkPhpSettings()
    {
        $displayErrors = $this->getSetting('display_errors');
        if ($displayErrors && mb_strtolower($displayErrors) !== 'off') {
            throw new SecurityException(
                sprintf('Unacceptable `display_errors` value for production: "%s"', $displayErrors)
            );
        }
    }
}
