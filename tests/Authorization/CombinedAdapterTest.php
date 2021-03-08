<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Casbin\Model\Model as CasbinModel;
use Casbin\Persist\Adapter as CasbinAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CombinedAdapterTest extends TestCase
{
    /** @var CombinedAdapter - System Under Test */
    protected $sut;

    /** @var CasbinAdapter|MockObject */
    protected $defaultAdapterMock;

    /** @var CacheManager|MockObject */
    protected $cacheManagerMock;

    protected $exampleConfig = <<<'EOF'
[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _
EOF;

    public function setUp(): void
    {
        $this->defaultAdapterMock = $this->createMock(CasbinAdapter::class);

        $this->cacheManagerMock = $this->createMock(CacheManager::class);

        $this->sut = new CombinedAdapter($this->defaultAdapterMock, $this->cacheManagerMock);

        parent::setUp();
    }

    public function createSutWithoutCacheManager()
    {
        $this->cacheManagerMock = null;

        $this->sut = new CombinedAdapter($this->defaultAdapterMock);
    }

    /**
     * @return array
     */
    public function loadPolicyWithoutCacheManagerProvider(): array
    {
        $adapterMock0 = $this->createMock(CasbinAdapter::class);
        $adapterMock1 = $this->createMock(CasbinAdapter::class);
        $adapterMock2 = $this->createMock(CasbinAdapter::class);

        return [
            'no-adapters'  => [[]],
            'one-adapter'  => [[$adapterMock0]],
            'two-adapters' => [[$adapterMock1, $adapterMock2]],
        ];
    }

    /**
     * @dataProvider loadPolicyWithoutCacheManagerProvider
     *
     * @param array $adaptersMocks
     */
    public function testLoadPolicyCallsAdaptersByDefault(array $adaptersMocks)
    {
        $this->createSutWithoutCacheManager();

        $model = new CasbinModel();

        $this->defaultAdapterMock->expects($this->once())->method('loadPolicy')->with($model);

        foreach ($adaptersMocks as $adapter) {
            $adapter->expects($this->once())->method('loadPolicy')->with($model);
            $this->sut->registerAdapter($adapter);
        }

        $this->sut->loadPolicy($model);
    }

    public function testLoadPolicyCanLoadPoliciesFromCache()
    {
        $cacheData = ['g' => [], 'p' => []];

        $model = new CasbinModel();

        $this->cacheManagerMock->expects($this->once())->method('getAll')->willReturn($cacheData);

        $this->defaultAdapterMock->expects($this->never())->method('loadPolicy');

        $this->sut->loadPolicy($model);
    }

    public function testLoadPolicyCanPopulatePoliciesFromCache()
    {
        $cachedData = [
            'g' => [
                ['joe', 'admin', '', '', ','],
                ['jane', 'user', '', '', ','],
            ],
            'p' => [
                ['user', 'admin_resource_users', 'read', '', ','],
                ['admin', 'admin_resource_users', 'write', '', ','],
            ],
        ];

        $model = new CasbinModel();
        $model->loadModelFromText($this->exampleConfig);

        $this->cacheManagerMock->expects($this->once())->method('getAll')->willReturn($cachedData);

        $this->defaultAdapterMock->expects($this->never())->method('loadPolicy');

        $this->sut->loadPolicy($model);
    }

    public function testLoadPolicyCanStorePoliciesInCache()
    {
        $cachedData = [
            'g' => [
                ['joe', 'admin', '', '', ','],
                ['jane', 'user', '', '', ','],
            ],
            'p' => [
                ['user', 'admin_resource_users', 'read', '', ','],
                ['admin', 'admin_resource_users', 'write', '', ','],
            ],
        ];

        $model = new CasbinModel();
        $model->loadModelFromText($this->exampleConfig);

        $model->addPolicies('g', 'g', $cachedData['g']);
        $model->addPolicies('p', 'p', $cachedData['p']);

        $this->cacheManagerMock->expects($this->once())->method('getAll')->willReturn([]);

        $this->defaultAdapterMock->expects($this->once())->method('loadPolicy')->with($model);

        $this->cacheManagerMock->expects($this->once())->method('storeAll')->willReturn(true);

        $this->sut->loadPolicy($model);
    }

    public function testSavePolicyCallsDefaultAdapter()
    {
        $model = new CasbinModel();

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('savePolicy')
            ->with($model);

        $this->sut->savePolicy($model);
    }

    public function testAddPolicyCallsDefaultAdapter()
    {
        $sec   = 'foo';
        $ptype = 'bar';
        $rule  = 'baz';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('addPolicy')
            ->with($sec, $ptype, [$rule]);

        $this->sut->addPolicy($sec, $ptype, [$rule]);
    }

    public function testRemovePolicyCallsDefaultAdapter()
    {
        $sec   = 'foo';
        $ptype = 'bar';
        $rule  = 'baz';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('removePolicy')
            ->with($sec, $ptype, [$rule]);

        $this->sut->removePolicy($sec, $ptype, [$rule]);
    }

    public function testRemoveFilteredPolicyWithoutFieldValuesCallsDefaultAdapter()
    {
        $sec        = 'foo';
        $ptype      = 'bar';
        $fieldIndex = 0;

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('removeFilteredPolicy')
            ->with($sec, $ptype, $fieldIndex);

        $this->sut->removeFilteredPolicy($sec, $ptype, $fieldIndex);
    }

    public function testRemoveFilteredPolicyWithFieldValuesCallsDefaultAdapter()
    {
        $sec         = 'foo';
        $ptype       = 'bar';
        $fieldIndex  = 0;
        $fieldValue0 = 'quix';
        $fieldValue1 = 'tata';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('removeFilteredPolicy')
            ->with($sec, $ptype, $fieldIndex, $fieldValue0, $fieldValue1);

        $this->sut->removeFilteredPolicy($sec, $ptype, $fieldIndex, $fieldValue0, $fieldValue1);
    }
}
