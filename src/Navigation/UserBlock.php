<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Contentless;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\INodeContainer;
use AbterPhp\Framework\Html\NodeContainerTrait;
use AbterPhp\Framework\Html\Tag;
use Opulence\Sessions\ISession;

class UserBlock extends Tag implements INodeContainer
{
    const DEFAULT_TAG = Html5::TAG_A;

    const AVATAR_BASE_URL = 'https://www.gravatar.com/avatar/%1$s';

    /** @var ISession */
    protected $session;

    /** @var INode */
    protected $mediaLeft;

    /** @var INode */
    protected $mediaBody;

    /** @var INode */
    protected $mediaRight;

    use NodeContainerTrait;

    /**
     * UserBlock constructor.
     *
     * @param ISession     $session
     * @param string[]     $intents
     * @param array        $attributes
     * @param string|null  $tag
     */
    public function __construct(
        ISession $session,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        $this->session      = $session;

        if (!$this->session->has(Session::USERNAME)) {
            throw new \LogicException('session must be set');
        }

        $username = (string)$this->session->get(Session::USERNAME, '');

        $this->mediaLeft  = new Component($this->getUserImage($username), [], [], Html5::TAG_DIV);
        $this->mediaBody  = new Component($username, [], [], Html5::TAG_DIV);
        $this->mediaRight = new Component(null, [], [], Html5::TAG_DIV);

        parent::__construct(null, $intents, $attributes, $tag);
    }

    /**
     * @param string $username
     *
     * @return INode
     */
    protected function getUserImage(string $username): INode
    {
        if (!$this->session->has(Session::EMAIL) || !$this->session->get(Session::IS_GRAVATAR_ALLOWED)) {
            return $this->getDefaultUserImage($username);
        }

        $emailHash = md5((string)$this->session->get(Session::EMAIL));
        $url       = sprintf(static::AVATAR_BASE_URL, $emailHash);

        $img     = new Contentless([], [Html5::ATTR_SRC => $url, Html5::ATTR_ALT => $username], Html5::TAG_IMG);
        $style   = sprintf('background: url(%1$s) no-repeat;', $url);
        $attribs = [Html5::ATTR_CLASS => 'user-img', Html5::ATTR_STYLE => $style];

        return new Component($img, [], $attribs, Html5::TAG_DIV);
    }

    /**
     * @param string $username
     *
     * @return INode
     */
    protected function getDefaultUserImage(string $username): INode
    {
        $url = 'https://via.placeholder.com/40/09f/fff.png';

        return new Contentless([], [Html5::ATTR_SRC => $url, Html5::ATTR_ALT => $username]);
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return array_merge([$this->mediaLeft, $this->mediaBody, $this->mediaRight], $this->getNodes());
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        return [];
    }

    /**
     * @return INode
     */
    public function getMediaLeft(): INode
    {
        return $this->mediaLeft;
    }

    /**
     * @param INode $mediaLeft
     */
    public function setMediaLeft(INode $mediaLeft): void
    {
        $this->mediaLeft = $mediaLeft;
    }

    /**
     * @return INode
     */
    public function getMediaBody(): INode
    {
        return $this->mediaBody;
    }

    /**
     * @param INode $mediaBody
     */
    public function setMediaBody(INode $mediaBody): void
    {
        $this->mediaBody = $mediaBody;
    }

    /**
     * @return INode
     */
    public function getMediaRight(): INode
    {
        return $this->mediaRight;
    }

    /**
     * @param INode $mediaRight
     */
    public function setMediaRight(INode $mediaRight): void
    {
        $this->mediaRight = $mediaRight;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content   = [];
        $content[] = (string)$this->mediaLeft;
        $content[] = (string)$this->mediaBody;
        $content[] = (string)$this->mediaRight;

        return StringHelper::wrapInTag(implode("\n", $content), $this->tag, $this->attributes);
    }
}
