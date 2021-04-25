<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Session as SessionConstant;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserBlockTest extends TestCase
{
    /** @var ISession|MockObject */
    protected $sessionMock;

    protected array $sessionData = [
        SessionConstant::USERNAME            => 'Mr. Wolf',
        SessionConstant::EMAIL               => 'mrwolf@example.com',
        SessionConstant::IS_GRAVATAR_ALLOWED => true,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->sessionMock = $this->createMock(Session::class);

        $sessionData = &$this->sessionData;
        $this->sessionMock->expects($this->any())->method('has')->willReturnCallback(
            fn ($key) => isset($sessionData[$key])
        );
        $this->sessionMock->expects($this->any())->method('get')->willReturnCallback(
            fn ($key) => $sessionData[$key] ?? ''
        );
    }

    public function testConstructThrowsExceptionIfUsernameIsNotRetrievable(): void
    {
        $this->expectException(\LogicException::class);

        /** @var ISession|MockObject $sessionMock */
        $sessionMock = $this->createMock(Session::class);

        $sessionMock->expects($this->any())->method('has')->willReturn(false);

        new UserBlock($sessionMock);
    }

    public function testFallbackUserImageIsUsedIfGravatarIsNotAllowed(): void
    {
        $sessionData = [
            SessionConstant::USERNAME            => 'Mr. Wolf',
            SessionConstant::EMAIL               => 'mrwolf@example.com',
            SessionConstant::IS_GRAVATAR_ALLOWED => false,
        ];

        /** @var ISession|MockObject $sessionMock */
        $sessionMock = $this->createMock(Session::class);

        $sessionMock->expects($this->any())->method('has')->willReturnCallback(
            fn ($key) => isset($sessionData[$key])
        );
        $sessionMock->expects($this->any())->method('get')->willReturnCallback(
            fn ($key) => $sessionData[$key] ?? ''
        );

        $sut = new UserBlock($sessionMock);

        $rendered = (string)$sut;

        $this->assertStringContainsString(
            '<a><div><span src="https://via.placeholder.com/40/09f/fff.png" alt="Mr. Wolf"></span></div>',
            $rendered
        ); // nolint
        $this->assertStringContainsString('<div>Mr. Wolf</div>', $rendered);
        $this->assertStringContainsString('<div></div></a>', $rendered);
    }

    public function testDefaultToString(): void
    {
        $sut = $this->createUserBlock();

        $rendered = (string)$sut;

        $this->assertStringContainsString(
            '<a><div><div class="user-img" style="background: url(https://www.gravatar.com/avatar/2ea036c591050aa0bd31e5034c18012f) no-repeat;"><img src="https://www.gravatar.com/avatar/2ea036c591050aa0bd31e5034c18012f" alt="Mr. Wolf"></div></div>', // phpcs:ignore
            $rendered
        );
        $this->assertStringContainsString('<div>Mr. Wolf</div>', $rendered);
        $this->assertStringContainsString('<div></div></a>', $rendered);
    }

    public function testToStringWithOptionalsSet(): void
    {
        $sut = $this->createUserBlock();

        $sut->setMediaLeft(new Node('AAA'));
        $sut->setMediaBody(new Node('BBB'));
        $sut->setMediaRight(new Node('CCC'));

        $rendered = (string)$sut;

        $this->assertStringContainsString("<a>AAA\nBBB\nCCC</a>", $rendered);
    }

    public function testGetNodes(): void
    {
        $expectedNodes = [];

        $sut = $this->createUserBlock();

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedNodes(): void
    {
        $sut = $this->createUserBlock();

        $actualResult = $sut->getExtendedNodes();

        $this->assertCount(3, $actualResult);
        $this->assertInstanceOf(Node::class, $actualResult[0]);
        $this->assertInstanceOf(Node::class, $actualResult[1]);
        $this->assertInstanceOf(Node::class, $actualResult[2]);
    }

    public function testGetMediaLeftReturnsTheLastSetMediaLeft(): void
    {
        /** @var Node|MockObject $mediaLeft */
        $mediaLeft = $this->createMock(Node::class);

        $sut = $this->createUserBlock();

        $sut->setMediaLeft($mediaLeft);

        $actualResult = $sut->getMediaLeft();

        $this->assertSame($mediaLeft, $actualResult);
    }

    public function testGetMediaRightReturnsTheLastSetMediaRight(): void
    {
        /** @var Node|MockObject $mediaRight */
        $mediaRight = $this->createMock(Node::class);

        $sut = $this->createUserBlock();

        $sut->setMediaRight($mediaRight);

        $actualResult = $sut->getMediaRight();

        $this->assertSame($mediaRight, $actualResult);
    }

    public function testGetMediaBodyReturnsTheLastSetMediaBody(): void
    {
        /** @var Node|MockObject $mediaBody */
        $mediaBody = $this->createMock(Node::class);

        $sut = $this->createUserBlock();

        $sut->setMediaBody($mediaBody);

        $actualResult = $sut->getMediaBody();

        $this->assertSame($mediaBody, $actualResult);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return UserBlock
     */
    protected function createUserBlock($content = null): UserBlock
    {
        $userBlock = new UserBlock($this->sessionMock);

        if ($content) {
            $userBlock->getMediaRight()->setContent($content);
        }

        return $userBlock;
    }
}
