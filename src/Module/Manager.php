<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Module;

use AbterPhp\Framework\Constant\Module;
use Opulence\Cache\ICacheBridge;
use Opulence\Console\Commands\Command;
use Opulence\Ioc\Bootstrappers\Bootstrapper;

class Manager
{
    const CACHE_KEY_HTTP_BOOTSTRAPPERS = 'AbterPhp:HttpBootstrappers';
    const CACHE_KEY_CLI_BOOTSTRAPPERS  = 'AbterPhp:CliBootstrappers';
    const CACHE_KEY_COMMANDS           = 'AbterPhp:Commands';
    const CACHE_KEY_ROUTE_PATHS        = 'AbterPhp:RoutePaths';
    const CACHE_KEY_EVENTS             = 'AbterPhp:Events';
    const CACHE_KEY_MIDDLEWARE         = 'AbterPhp:Middleware';
    const CACHE_KEY_MIGRATION_PATHS    = 'AbterPhp:MigrationPaths';
    const CACHE_KEY_RESOURCE_PATH      = 'AbterPhp:Resource';

    /** @var Loader */
    protected $loader;

    /** @var ICacheBridge|null */
    protected $cacheBridge;

    /** @var array|null */
    protected $modules;

    /**
     * Manager constructor.
     *
     * @param Loader            $sourceRoots
     * @param ICacheBridge|null $cacheBridge
     */
    public function __construct(Loader $loader, ?ICacheBridge $cacheBridge = null)
    {
        $this->loader      = $loader;
        $this->cacheBridge = $cacheBridge;
    }

    /**
     * @return Bootstrapper[]
     */
    protected function cacheWrapper(string $cacheKey, callable $callback): array
    {
        // phpcs:disable Generic.CodeAnalysis.EmptyStatement
        try {
            if ($this->cacheBridge && $this->cacheBridge->has($cacheKey)) {
                return (array)$this->cacheBridge->get($cacheKey);
            }
        } catch (\Exception $e) {
            // It's always safe to skip reading the cache
        }
        // phpcs:enable Generic.CodeAnalysis.EmptyStatement

        $this->init();

        $bootstrappers = call_user_func($callback, $this->modules);

        // phpcs:disable Generic.CodeAnalysis.EmptyStatement
        try {
            if ($this->cacheBridge) {
                $this->cacheBridge->set($cacheKey, $bootstrappers, PHP_INT_MAX);
            }
        } catch (\Exception $e) {
            // It's always safe to skip writing the cache
        }
        // phpcs:enable Generic.CodeAnalysis.EmptyStatement

        return $bootstrappers;
    }

    /**
     * @return Bootstrapper[]
     */
    public function getHttpBootstrappers(): array
    {
        $callback = function (array $modules) {
            $bootstrappers = [];
            foreach ($modules as $module) {
                if (isset($module[Module::BOOTSTRAPPERS])) {
                    $bootstrappers = array_merge($bootstrappers, $module[Module::BOOTSTRAPPERS]);
                }
                if (isset($module[Module::HTTP_BOOTSTRAPPERS])) {
                    $bootstrappers = array_merge($bootstrappers, $module[Module::HTTP_BOOTSTRAPPERS]);
                }
            }

            return $bootstrappers;
        };

        return $this->cacheWrapper(static::CACHE_KEY_HTTP_BOOTSTRAPPERS, $callback);
    }

    /**
     * @return Bootstrapper[]
     */
    public function getCliBootstrappers(): array
    {
        $callback = function (array $modules) {
            $bootstrappers = [];
            foreach ($modules as $module) {
                if (isset($module[Module::BOOTSTRAPPERS])) {
                    $bootstrappers = array_merge($bootstrappers, $module[Module::BOOTSTRAPPERS]);
                }
                if (isset($module[Module::CLI_BOOTSTRAPPERS])) {
                    $bootstrappers = array_merge($bootstrappers, $module[Module::CLI_BOOTSTRAPPERS]);
                }
            }

            return $bootstrappers;
        };

        return $this->cacheWrapper(static::CACHE_KEY_CLI_BOOTSTRAPPERS, $callback);
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array
    {
        $callback = function (array $modules) {
            $commands = [];
            foreach ($modules as $module) {
                if (isset($module[Module::COMMANDS])) {
                    $commands = array_merge($commands, $module[Module::COMMANDS]);
                }
            }

            return $commands;
        };

        return $this->cacheWrapper(static::CACHE_KEY_COMMANDS, $callback);
    }

    /**
     * @return string[][]
     */
    public function getEvents(): array
    {
        return $this->cacheWrapper(static::CACHE_KEY_EVENTS, $this->namedOptionsCallback(Module::EVENTS));
    }

    /**
     * @return string[][]
     */
    public function getMiddleware(): array
    {
        return $this->cacheWrapper(
            static::CACHE_KEY_MIDDLEWARE,
            $this->prioritizedOptionsCallback(Module::MIDDLEWARE)
        );
    }

    /**
     * @return string[][]
     */
    public function getRoutePaths(): array
    {
        return $this->cacheWrapper(
            static::CACHE_KEY_ROUTE_PATHS,
            $this->prioritizedOptionsCallback(Module::ROUTE_PATHS, true)
        );
    }

    /**
     * @return string[][]
     */
    public function getMigrationPaths(): array
    {
        return $this->cacheWrapper(
            static::CACHE_KEY_MIGRATION_PATHS,
            $this->prioritizedOptionsCallback(Module::MIGRATION_PATHS)
        );
    }

    /**
     * @return string[]
     */
    public function getResourcePaths(): array
    {
        return $this->cacheWrapper(
            static::CACHE_KEY_RESOURCE_PATH,
            $this->simpleOptionCallback(Module::RESOURCE_PATH)
        );
    }

    /**
     * Creates a callback that will simply merge a 2-dimensions array
     *
     * Examples
     * Module A: ['A' => ['a', 'b', 'c'], 'B' => ['d', 'b']]
     * Module B: ['A' => ['a', 'b'], 'C' => ['a']]
     * Result:   ['A' => ['a', 'b', 'c', 'a', 'b'], 'B' => ['d', 'b'], 'C' => ['a']]
     *
     * @param string $option
     *
     * @return callable
     */
    protected function namedOptionsCallback(string $option): callable
    {
        return function ($modules) use ($option) {
            $merged = [];
            foreach ($modules as $module) {
                if (!isset($module[$option])) {
                    continue;
                }
                foreach ($module[$option] as $eventType => $events) {
                    if (!isset($merged[$eventType])) {
                        $merged[$eventType] = [];
                    }
                    $merged[$eventType] = array_merge($merged[$eventType], $events);
                }
            }

            return $merged;
        };
    }

    /**
     * Creates a callback that will try to keep the prioritization of the options in place
     *
     * Examples
     * Module A: [3 => ['a', 'b', 'c'], 10 => ['d', 'b'], 12 => ['a']]
     * Module B: [10 => ['a', 'b'], 14 => ['a']]
     * Result:   [3 => ['a', 'b', 'c'], 10 => ['d', 'b', 'a', 'b'], 12 => ['a'], 14 => ['a']]
     *
     * @param string $option
     * @param bool   $reversed
     *
     * @return callable
     */
    protected function prioritizedOptionsCallback(string $option, bool $reversed = false): callable
    {
        return function ($modules) use ($option, $reversed) {
            $merged = [];
            foreach ($modules as $module) {
                if (!isset($module[$option])) {
                    continue;
                }
                foreach ($module[$option] as $priority => $priorityPaths) {
                    if (!isset($merged[$priority])) {
                        $merged[$priority] = [];
                    }
                    $merged[$priority] = array_merge($merged[$priority], $priorityPaths);
                }
            }

            if ($reversed) {
                krsort($merged);
            } else {
                ksort($merged);
            }

            $flattened = [];
            foreach ($merged as $priorityPaths) {
                $flattened = array_merge($flattened, $priorityPaths);
            }

            return $flattened;
        };
    }

    /**
     * Creates a callback that will return a simple option
     *
     * Examples
     * Module A: [3 => ['a', 'b', 'c'], 10 => ['d', 'b'], 12 => ['a']]
     * Module B: [10 => ['a', 'b'], 14 => ['a']]
     * Result:   [3 => ['a', 'b', 'c'], 10 => ['d', 'b', 'a', 'b'], 12 => ['a'], 14 => ['a']]
     *
     * @param string $option
     * @param bool   $reversed
     *
     * @return callable
     */
    protected function simpleOptionCallback(string $option): callable
    {
        return function ($modules) use ($option) {
            $merged = [];
            foreach ($modules as $module) {
                if (!isset($module[$option])) {
                    continue;
                }
                $merged[$module[Module::IDENTIFIER]] = $module[$option];
            }

            return $merged;
        };
    }



    /**
     * @return $this
     */
    protected function init(): Manager
    {
        if ($this->modules) {
            return $this;
        }

        $this->modules = $this->loader->loadModules();

        return $this;
    }
}
