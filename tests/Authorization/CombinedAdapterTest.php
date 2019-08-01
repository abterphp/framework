<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Casbin\Model\Model as CasbinModel;
use Casbin\Persist\Adapter as CasbinAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CombinedAdapterTest extends TestCase
{
    /** @var CombinedAdapter */
    protected $sut;

    /** @var CasbinAdapter|MockObject */
    protected $defaultAdapterMock;

    /** @var CacheManager|MockObject */
    protected $cacheManagerMock;

    public function setUp(): void
    {
        $this->defaultAdapterMock = $this->getMockBuilder(CasbinAdapter::class)
            ->setMethods(['loadPolicy', 'savePolicy', 'addPolicy', 'removePolicy', 'removeFilteredPolicy'])
            ->getMock();

        $this->cacheManagerMock = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAll', 'storeAll'])
            ->getMock();

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
        $adapterMock0 = $this->getMockBuilder(CasbinAdapter::class)
            ->setMethods(['loadPolicy', 'savePolicy', 'addPolicy', 'removePolicy', 'removeFilteredPolicy'])
            ->getMock();
        $adapterMock1 = $this->getMockBuilder(CasbinAdapter::class)
            ->setMethods(['loadPolicy', 'savePolicy', 'addPolicy', 'removePolicy', 'removeFilteredPolicy'])
            ->getMock();
        $adapterMock2 = $this->getMockBuilder(CasbinAdapter::class)
            ->setMethods(['loadPolicy', 'savePolicy', 'addPolicy', 'removePolicy', 'removeFilteredPolicy'])
            ->getMock();

        return [
            'no-adapters'  => [[]],
            'one-adapter'  => [[$adapterMock0]],
            'two-adapters' => [[$adapterMock1, $adapterMock2]],
        ];
    }

    /**
     * @dataProvider loadPolicyWithoutCacheManagerProvider
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
            ]
        ];

        $model = new CasbinModel();

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
            ]
        ];

        $model = new CasbinModel();

        foreach ($cachedData['g'] as $policy) {
            $model->model['g']['g']->policy[] = $policy;
        }

        foreach ($cachedData['p'] as $policy) {
            $model->model['p']['p']->policy[] = $policy;
        }

        $this->cacheManagerMock->expects($this->once())->method('getAll')->willReturn([]);

        $this->defaultAdapterMock->expects($this->once())->method('loadPolicy')->with($model);

        $this->cacheManagerMock->expects($this->once())->method('storeAll')->willReturn(true);

        $this->sut->loadPolicy($model);
    }

    public function testSavePolicyCallsDefaultAdapter()
    {
        $model          = new \Casbin\Model\Model();
        $expectedResult = 'foo';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('savePolicy')
            ->with($model)
            ->willReturn($expectedResult);

        $actualResult = $this->sut->savePolicy($model);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddPolicyCallsDefaultAdapter()
    {
        $sec            = 'foo';
        $ptype          = 'bar';
        $rule           = 'baz';
        $expectedResult = 'foo';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('addPolicy')
            ->with($sec, $ptype, $rule)
            ->willReturn($expectedResult);

        $actualResult = $this->sut->addPolicy($sec, $ptype, $rule);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRemovePolicyCallsDefaultAdapter()
    {
        $sec            = 'foo';
        $ptype          = 'bar';
        $rule           = 'baz';
        $expectedResult = 'foo';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('removePolicy')
            ->with($sec, $ptype, $rule)
            ->willReturn($expectedResult);

        $actualResult = $this->sut->removePolicy($sec, $ptype, $rule);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRemoveFilteredPolicyWithoutFieldValuesCallsDefaultAdapter()
    {
        $sec            = 'foo';
        $ptype          = 'bar';
        $rule           = 'baz';
        $expectedResult = 'foo';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('removeFilteredPolicy')
            ->with($sec, $ptype, $rule)
            ->willReturn($expectedResult);

        $actualResult = $this->sut->removeFilteredPolicy($sec, $ptype, $rule);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRemoveFilteredPolicyWithFieldValuesCallsDefaultAdapter()
    {
        $sec            = 'foo';
        $ptype          = 'bar';
        $rule           = 'baz';
        $fieldValue0    = 'quix';
        $fieldValue1    = 'tata';
        $expectedResult = 'foo';

        $this->defaultAdapterMock
            ->expects($this->once())
            ->method('removeFilteredPolicy')
            ->with($sec, $ptype, $rule, $fieldValue0, $fieldValue1)
            ->willReturn($expectedResult);

        $actualResult = $this->sut->removeFilteredPolicy($sec, $ptype, $rule, $fieldValue0, $fieldValue1);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
