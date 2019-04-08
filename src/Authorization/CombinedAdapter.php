<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Casbin\Model\Model;
use Casbin\Persist\Adapter as CasbinAdapter;

class CombinedAdapter implements CasbinAdapter
{
    /** @var CasbinAdapter */
    protected $defaultAdapter;

    /** @var CacheManager|null */
    protected $cacheManager;

    /** @var CasbinAdapter[] */
    protected $registeredAdapters = [];

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
    public function registerAdapter(CasbinAdapter $adapter)
    {
        $this->registeredAdapters[] = $adapter;

        return $this;
    }

    /**
     * @param Model $model
     */
    public function loadPolicy($model)
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
    protected function loadCachedPolicy($model): bool
    {
        if (!$this->cacheManager) {
            return false;
        }

        $cachedData = $this->cacheManager->getAll();

        if (!$cachedData || !array_key_exists('g', $cachedData) || !array_key_exists('p', $cachedData)) {
            return false;
        }

        foreach ($cachedData['g'] as $policy) {
            $model->model['g']['g']->policy[] = $policy;
        }

        foreach ($cachedData['p'] as $policy) {
            $model->model['p']['p']->policy[] = $policy;
        }

        return true;
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    protected function storeLoadedPolicies($model): bool
    {
        if (!$this->cacheManager) {
            return false;
        }

        $data = [
            'g' => $model->model['g']['g']->policy,
            'p' => $model->model['p']['p']->policy,
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
     *
     * @return mixed
     */
    public function savePolicy($model)
    {
        return $this->defaultAdapter->savePolicy($model);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param string $rule
     *
     * @return mixed
     */
    public function addPolicy($sec, $ptype, $rule)
    {
        return $this->defaultAdapter->addPolicy($sec, $ptype, $rule);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param string $rule
     *
     * @return mixed
     */
    public function removePolicy($sec, $ptype, $rule)
    {
        return $this->defaultAdapter->removePolicy($sec, $ptype, $rule);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param string $fieldIndex
     * @param string ...$fieldValues
     *
     * @return mixed
     */
    public function removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues)
    {
        $args = array_merge([$sec, $ptype, $fieldIndex], $fieldValues);

        return call_user_func_array([$this->defaultAdapter, 'removeFilteredPolicy'], $args);
    }
}
