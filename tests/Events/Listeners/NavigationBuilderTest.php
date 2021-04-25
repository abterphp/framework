<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events\Listeners;

use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Navigation\Item;
use AbterPhp\Framework\Navigation\Navigation;
use Casbin\Enforcer;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase;

class NavigationBuilderTest extends TestCase
{
    protected const USERNAME = 'foo';

    /** @var NavigationBuilder - System Under Test */
    protected NavigationBuilder $sut;

    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    public function setUp(): void
    {
        $this->sessionMock = $this->createMock(ISession::class);
        $this->sessionMock->expects($this->any())->method('get')->willReturn(static::USERNAME);

        $this->enforcerMock = $this->createMock(Enforcer::class);

        $this->sut = new NavigationBuilder($this->sessionMock, $this->enforcerMock);

        parent::setUp();
    }

    public function testHandleWorksWithoutNodes(): void
    {
        $navigationReady = $this->createNavigationEvent([], $this->once());

        $this->sut->handle($navigationReady);
    }

    public function testHandleWorksWithNonResourceNodes(): void
    {
        $navigationReady = $this->createNavigationEvent([new Node()], $this->once());

        $this->sut->handle($navigationReady);
    }

    public function testHandleSkipsItemsWithoutRoleAndResource(): void
    {
        $itemMock = $this->createMock(Item::class);
        $itemMock->expects($this->once())->method('getResource')->willReturn(null);
        $itemMock->expects($this->any())->method('getRole')->willReturn('');
        $itemMock->expects($this->never())->method('disable');

        $navigationReady = $this->createNavigationEvent([$itemMock], $this->once());

        $this->sut->handle($navigationReady);
    }

    public function testHandleSkipItemsTheUserHasAccessTo(): void
    {
        $role     = 'foo';
        $resource = 'bar';

        $itemMock = $this->createMock(Item::class);
        $itemMock->expects($this->any())->method('getResource')->willReturn($resource);
        $itemMock->expects($this->any())->method('getRole')->willReturn($role);
        $itemMock->expects($this->never())->method('disable');

        $this->enforcerMock->expects($this->once())->method('enforce')->willReturn(true);

        $navigationReady = $this->createNavigationEvent([$itemMock], $this->once());

        $this->sut->handle($navigationReady);
    }

    public function testHandleDisableItemsTheUserHasNoAccessTo(): void
    {
        $role     = 'foo';
        $resource = 'bar';

        $itemMock = $this->createMock(Item::class);
        $itemMock->expects($this->any())->method('getResource')->willReturn($resource);
        $itemMock->expects($this->any())->method('getRole')->willReturn($role);
        $itemMock->expects($this->once())->method('disable');

        $this->enforcerMock->expects($this->once())->method('enforce')->willReturn(false);

        $navigationReady = $this->createNavigationEvent([$itemMock], $this->once());

        $this->sut->handle($navigationReady);
    }

    /**
     * @param array                    $nodes
     * @param InvokedCountMatcher|null $expects
     *
     * @return NavigationReady
     */
    protected function createNavigationEvent(array $nodes, InvokedCountMatcher $expects = null): NavigationReady
    {
        $expects = $expects === null ? $this->any() : $expects;

        /** @var MockObject|Navigation $navigationStub */
        $navigationStub = $this->createMock(Navigation::class);

        $navigationStub->expects($expects)->method('getExtendedNodes')->willReturn($nodes);

        return new NavigationReady($navigationStub);
    }
}
