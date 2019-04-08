<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Session as SessionConstant;
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

    /** @var array */
    protected $sessionData = [
        SessionConstant::USERNAME            => 'Mr. Wolf',
        SessionConstant::EMAIL               => 'mrwolf@example.com',
        SessionConstant::IS_GRAVATAR_ALLOWED => true,
    ];

    public function setUp()
    {
        parent::setUp();

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has'])
            ->getMock();

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

    /**
     * @expectedException \LogicException
     */
    public function testConstructThrowsExceptionIfUsernameIsNotRetrievable()
    {
        /** @var ISession|MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has'])
            ->getMock();

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
        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has'])
            ->getMock();

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

        $this->assertContains('<a><div><span src="https://via.placeholder.com/40/09f/fff.png" alt="Mr. Wolf"></div>', $rendered); // nolint
        $this->assertContains('<div>Mr. Wolf</div>', $rendered);
        $this->assertContains('<div></div></a>', $rendered);
    }

    public function testDefaultToString()
    {
        $sut = $this->createNode();

        $rendered = (string)$sut;

        $this->assertContains('<a><div><div class="user-img" style="background: url(https://www.gravatar.com/avatar/2ea036c591050aa0bd31e5034c18012f) no-repeat;"><img src="https://www.gravatar.com/avatar/2ea036c591050aa0bd31e5034c18012f" alt="Mr. Wolf"></div></div>', $rendered); // nolint
        $this->assertContains('<div>Mr. Wolf</div>', $rendered);
        $this->assertContains('<div></div></a>', $rendered);
    }

    public function testToStringWithOptionalsSet()
    {
        $sut = $this->createNode();

        $sut->setMediaLeft(new Node('AAA'));
        $sut->setMediaBody(new Node('BBB'));
        $sut->setMediaRight(new Node('CCC'));

        $rendered = (string)$sut;

        $this->assertContains("<a>AAA\nBBB\nCCC</a>", $rendered);
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
