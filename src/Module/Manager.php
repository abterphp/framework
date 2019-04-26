<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Module;

use AbterPhp\Framework\Constant\Module;
use Opulence\Cache\ICacheBridge;
use Opulence\Console\Commands\Command;
use Opulence\Ioc\Bootstrappers\Bootstrapper;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Manager
{
    const CACHE_KEY_HTTP_BOOTSTRAPPERS = 'AbterPhp:HttpBootstrappers';
    const CACHE_KEY_CLI_BOOTSTRAPPERS  = 'AbterPhp:CliBootstrappers';
    const CACHE_KEY_COMMANDS           = 'AbterPhp:Commands';
    const CACHE_KEY_ROUTE_PATHS        = 'AbterPhp:RoutePaths';
    const CACHE_KEY_EVENTS             = 'AbterPhp:Events';
    const CACHE_KEY_MIDDLEWARE         = 'AbterPhp:Middleware';
    const CACHE_KEY_MIGRATION_PATHS    = 'AbterPhp:MigrationPaths';
    const CACHE_KEY_RESOURCE_PATH      = 'AbterPhp:ResourcePaths';
    const CACHE_KEY_ASSETS_PATHS       = 'AbterPhp:AssetsPaths';
    const CACHE_KEY_VIEWS              = 'AbterPhp:Views';

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
        return $this->cacheWrapper(static::CACHE_KEY_EVENTS, $this->namedPrioritizedOptionsCallback(Module::EVENTS));
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
     * @return string[]
     */
    public function getAssetsPaths(): array
    {
        return $this->cacheWrapper(
            static::CACHE_KEY_ASSETS_PATHS,
            $this->simpleNamedOptions(Module::ASSETS_PATHS)
        );
    }

    /**
     * Creates a callback that will return a simple option for each module it's defined for
     *
     * Examples
     * Module A: 'a'
     * Module B: 'b'
     * Result:   ['Module A' => 'a', 'Module B' => 'b']
     *
     * @param string $option
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
     * Creates a callback that allows overriding previously definid options
     *
     * Examples
     * Module A: ['a' => 'a', 'b' => 'b']
     * Module B: ['b' => 'c', 'c' => 'd']
     * Result:   ['a' => 'a', 'b' => 'c', 'c' => 'd']
     *
     * @param string $option
     *
     * @return callable
     */
    protected function simpleNamedOptions(string $option): callable
    {
        return function ($modules) use ($option) {
            $merged = [];
            foreach ($modules as $module) {
                if (!isset($module[$option])) {
                    continue;
                }
                foreach ($module[$option] as $key => $value) {
                    $merged[$key] = $value;
                }
            }

            return $merged;
        };
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * Creates a callback that will simply merge a 2-dimensions array
     *
     * Examples
     * Module A: ['A' => [5 => ['z'], 10 => ['a', 'b', 'c']], 'B' => [10 => ['d', 'b']]]
     * Module B: ['A' => [10 => ['a', 'b']], 'C' => [10 => ['a']]]
     * Result:   ['A' => ['z', 'a', 'b', 'c', 'a', 'b'], 'B' => ['d', 'b'], 'C' => ['a']]
     *
     * @param string $option
     *
     * @return callable
     */
    protected function namedPrioritizedOptionsCallback(string $option): callable
    {
        return function ($modules) use ($option) {
            $prioritized = [];
            foreach ($modules as $module) {
                if (!isset($module[$option])) {
                    continue;
                }
                foreach ($module[$option] as $eventType => $prioritizedEvents) {
                    if (!$prioritizedEvents) {
                        continue;
                    }
                    foreach ($prioritizedEvents as $priority => $events) {
                        if (!isset($prioritized[$eventType][$priority])) {
                            $prioritized[$eventType][$priority] = [];
                        }
                        $prioritized[$eventType][$priority] = array_merge($prioritized[$eventType][$priority], $events);
                    }
                }
            }

            $merged = [];
            foreach ($prioritized as $eventType => $events) {
                krsort($events);
                foreach ($events as $priority => $priorityEvents) {
                    if (empty($merged[$eventType])) {
                        $merged[$eventType] = [];
                    }
                    $merged[$eventType] = array_merge($merged[$eventType], $priorityEvents);
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
