<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Casbin\Model\Model;
use Casbin\Persist\Adapter as CasbinAdapter;

class CombinedAdapter implements CasbinAdapter
{
    protected CasbinAdapter $defaultAdapter;

    protected ?CacheManager $cacheManager;

    /** @var CasbinAdapter[] */
    protected array $registeredAdapters = [];

    /**
     * PolicyAdapter constructor.
     *
     * @param CasbinAdapter     $defaultAdapter
     * @param CacheManager|null $cacheManager
     */
    public function __construct(CasbinAdapter $defaultAdapter, ?CacheManager $cacheManager = null)
    {
        $this->defaultAdapter = $defaultAdapter;
        $this->cacheManager   = $cacheManager;
    }

    /**
     * @param CasbinAdapter $adapter
     *
     * @return $this
     */
    public function registerAdapter(CasbinAdapter $adapter): CombinedAdapter
    {
        $this->registeredAdapters[] = $adapter;

        return $this;
    }

    /**
     * @param Model $model
     */
    public function loadPolicy(Model $model): void
    {
        if ($this->loadCachedPolicy($model)) {
            return;
        }

        if ($this->loadAdapterPolicies($model)) {
            $this->storeLoadedPolicies($model);
        }
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    protected function loadCachedPolicy(Model $model): bool
    {
        if (!$this->cacheManager) {
            return false;
        }

        $cachedData = $this->cacheManager->getAll();

        if (!$cachedData || !array_key_exists('g', $cachedData) || !array_key_exists('p', $cachedData)) {
            return false;
        }

        $model->addPolicies('g', 'g', $cachedData['g']);
        $model->addPolicies('p', 'p', $cachedData['p']);

        return true;
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    protected function storeLoadedPolicies(Model $model): bool
    {
        if (!$this->cacheManager) {
            return false;
        }

        $data = [
            'g' => $model->getPolicy('g', 'g'),
            'p' => $model->getPolicy('p', 'p'),
        ];

        return $this->cacheManager->storeAll($data);
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    protected function loadAdapterPolicies($model): bool
    {
        $this->defaultAdapter->loadPolicy($model);

        foreach ($this->registeredAdapters as $adapter) {
            $adapter->loadPolicy($model);
        }

        return true;
    }

    /**
     * @param Model $model
     */
    public function savePolicy(Model $model): void
    {
        $this->defaultAdapter->savePolicy($model);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
        $this->defaultAdapter->addPolicy($sec, $ptype, $rule);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
        $this->defaultAdapter->removePolicy($sec, $ptype, $rule);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param string ...$fieldValues
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void
    {
        $this->defaultAdapter->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);
    }
}
