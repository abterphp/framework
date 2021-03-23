<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Session as SessionConstant;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\TagTest;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use PHPUnit\Framework\MockObject\MockObject;

class UserBlockTest extends TagTest
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
            function ($key) use ($sessionData) {
                return isset($sessionData[$key]);
            }
        );
        $this->sessionMock->expects($this->any())->method('get')->willReturnCallback(
            function ($key) use ($sessionData) {
                return isset($sessionData[$key]) ? $sessionData[$key] : '';
            }
        );
    }

    public function testConstructThrowsExceptionIfUsernameIsNotRetrievable()
    {
        $this->expectException(\LogicException::class);

        /** @var ISession|MockObject $sessionMock */
        $sessionMock = $this->createMock(Session::class);

        $sessionMock->expects($this->any())->method('has')->willReturn(false);

        new UserBlock($sessionMock);
    }

    public function testFallbackUserImageIsUsedIfGravatarIsNotAllowed()
    {
        $sessionData = [
            SessionConstant::USERNAME            => 'Mr. Wolf',
            SessionConstant::EMAIL               => 'mrwolf@example.com',
            SessionConstant::IS_GRAVATAR_ALLOWED => false,
        ];

        /** @var ISession|MockObject $sessionMock */
        $sessionMock = $this->createMock(Session::class);

        $sessionMock->expects($this->any())->method('has')->willReturnCallback(
            function ($key) use ($sessionData) {
                return isset($sessionData[$key]);
            }
        );
        $sessionMock->expects($this->any())->method('get')->willReturnCallback(
            function ($key) use ($sessionData) {
                return isset($sessionData[$key]) ? $sessionData[$key] : '';
            }
        );

        $sut = new UserBlock($sessionMock);

        $rendered = (string)$sut;

        $this->assertStringContainsString(
            '<a><div><span src="https://via.placeholder.com/40/09f/fff.png" alt="Mr. Wolf"></div>',
            $rendered
        ); // nolint
        $this->assertStringContainsString('<div>Mr. Wolf</div>', $rendered);
        $this->assertStringContainsString('<div></div></a>', $rendered);
    }

    public function testDefaultToString()
    {
        $sut = $this->createNode();

        $rendered = (string)$sut;

        $this->assertStringContainsString(
            '<a><div><div class="user-img" style="background: url(https://www.gravatar.com/avatar/2ea036c591050aa0bd31e5034c18012f) no-repeat;"><img src="https://www.gravatar.com/avatar/2ea036c591050aa0bd31e5034c18012f" alt="Mr. Wolf"></div></div>', // phpcs:ignore
            $rendered
        );
        $this->assertStringContainsString('<div>Mr. Wolf</div>', $rendered);
        $this->assertStringContainsString('<div></div></a>', $rendered);
    }

    public function testToStringWithOptionalsSet()
    {
        $sut = $this->createNode();

        $sut->setMediaLeft(new Node('AAA'));
        $sut->setMediaBody(new Node('BBB'));
        $sut->setMediaRight(new Node('CCC'));

        $rendered = (string)$sut;

        $this->assertStringContainsString("<a>AAA\nBBB\nCCC</a>", $rendered);
    }

    public function testGetNodes()
    {
        $expectedNodes = [];

        $sut = $this->createNode();

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedNodes()
    {
        $sut = $this->createNode();

        $actualResult = $sut->getExtendedNodes();

        $this->assertCount(3, $actualResult);
        $this->assertInstanceOf(Component::class, $actualResult[0]);
        $this->assertInstanceOf(Component::class, $actualResult[1]);
        $this->assertInstanceOf(Component::class, $actualResult[2]);
    }

    public function testGetMediaLeftReturnsINodeByDefault()
    {
        $sut = $this->createNode();

        $actualResult = $sut->getMediaLeft();

        $this->assertInstanceOf(INode::class, $actualResult);
    }

    public function testGetMediaRightReturnsINodeByDefault()
    {
        $sut = $this->createNode();

        $actualResult = $sut->getMediaRight();

        $this->assertInstanceOf(INode::class, $actualResult);
    }

    public function testGetMediaBodyReturnsINodeByDefault()
    {
        $sut = $this->createNode();

        $actualResult = $sut->getMediaBody();

        $this->assertInstanceOf(INode::class, $actualResult);
    }

    public function testGetMediaLeftReturnsTheLastSetMediaLeft()
    {
        /** @var IComponent $mediaLeft */
        $mediaLeft = $this->createMock(IComponent::class);

        $sut = $this->createNode();

        $sut->setMediaLeft($mediaLeft);

        $actualResult = $sut->getMediaLeft();

        $this->assertSame($mediaLeft, $actualResult);
    }

    public function testGetMediaRightReturnsTheLastSetMediaRight()
    {
        /** @var IComponent $mediaRight */
        $mediaRight = $this->createMock(IComponent::class);

        $sut = $this->createNode();

        $sut->setMediaRight($mediaRight);

        $actualResult = $sut->getMediaRight();

        $this->assertSame($mediaRight, $actualResult);
    }

    public function testGetMediaBodyReturnsTheLastSetMediaBody()
    {
        /** @var IComponent $mediaBody */
        $mediaBody = $this->createMock(IComponent::class);

        $sut = $this->createNode();

        $sut->setMediaBody($mediaBody);

        $actualResult = $sut->getMediaBody();

        $this->assertSame($mediaBody, $actualResult);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return UserBlock
     */
    protected function createNode($content = null): INode
    {
        $userBlock = new UserBlock($this->sessionMock);

        if ($content) {
            $userBlock->getMediaRight()->setContent($content);
        }

        return $userBlock;
    }
}
