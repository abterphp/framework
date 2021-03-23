<?php

namespace AbterPhp\Framework\Bootstrappers\Authorization;

use AbterPhp\Framework\Authorization\CombinedAdapter;
use AbterPhp\Framework\Authorization\Constant\Role;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Casbin\Enforcer;
use Opulence\Ioc\Container;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use PHPUnit\Framework\TestCase;

class EnforcerBootstrapperTest extends TestCase
{
    /** @var EnforcerBootstrapper - System Under Test */
    protected EnforcerBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new EnforcerBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::DIR_AUTH_CONFIG);
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function testRegisterBindingsWithoutSession()
    {
        Environment::setVar(Env::DIR_AUTH_CONFIG, '/tmp');
        Environment::setVar(Env::ENV_NAME, 'foo');

        file_put_contents('/tmp/model.conf', '');

        $mockAdapter = $this->getMockBuilder(CombinedAdapter::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(CombinedAdapter::class, $mockAdapter);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Enforcer::class);
        $this->assertInstanceOf(Enforcer::class, $actual);
    }

    public function testRegisterBindingsWithSession()
    {
        $usernameStub = 'Foo';

        Environment::setVar(Env::DIR_AUTH_CONFIG, '/tmp');
        Environment::setVar(Env::ENV_NAME, 'foo');

        file_put_contents('/tmp/model.conf', '');

        $mockAdapter = $this->getMockBuilder(CombinedAdapter::class)->disableOriginalConstructor()->getMock();
        $sessionMock = $this->getMockBuilder(ISession::class)->getMock();
        $sessionMock->expects($this->any())->method('get')->with()->willReturn($usernameStub);
        $mockTranspiler = $this->getMockBuilder(ITranspiler::class)->getMock();

        $container = new Container();
        $container->bindInstance(CombinedAdapter::class, $mockAdapter);
        $container->bindInstance(ISession::class, $sessionMock);
        $container->bindInstance(ITranspiler::class, $mockTranspiler);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Enforcer::class);
        $this->assertInstanceOf(Enforcer::class, $actual);
    }

    /**
     * @return array
     */
    public function createCanViewFunctionProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider createCanViewFunctionProvider
     *
     * @param bool $expectedResult
     */
    public function testCreateCanViewViewFunctionChecksReadRole(bool $expectedResult)
    {
        $usernameStub = 'foo';
        $keyStub      = 'bar';

        $enforcerMock = $this->getMockBuilder(Enforcer::class)->disableOriginalConstructor()->getMock();
        $enforcerMock
            ->expects($this->once())
            ->method('enforce')
            ->with($usernameStub, "admin_resource_$keyStub", Role::READ)
            ->willReturn($expectedResult);

        $actual = $this->sut->createCanViewViewFunction($usernameStub, $enforcerMock)($keyStub);
        $this->assertEquals($expectedResult, $actual);
    }
}
