<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Contentless;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\Helper\Tag as TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;
use Opulence\Sessions\ISession;

class UserBlock extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_A;

    public const AVATAR_BASE_URL = 'https://www.gravatar.com/avatar/%1$s';

    protected ISession $session;

    protected INode $mediaLeft;
    protected INode $mediaBody;
    protected INode $mediaRight;

    /**
     * UserBlock constructor.
     *
     * @param ISession                     $session
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(
        ISession $session,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        $this->session = $session;

        if (!$this->session->has(Session::USERNAME)) {
            throw new \LogicException('session must be set');
        }

        $username = (string)$this->session->get(Session::USERNAME, '');

        $this->mediaLeft  = new Tag($this->getUserImage($username), [], null, Html5::TAG_DIV);
        $this->mediaBody  = new Tag($username, [], null, Html5::TAG_DIV);
        $this->mediaRight = new Tag(null, [], null, Html5::TAG_DIV);

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

        $emailHash  = md5((string)$this->session->get(Session::EMAIL));
        $url        = sprintf(static::AVATAR_BASE_URL, $emailHash);
        $attributes = Attributes::fromArray([Html5::ATTR_SRC => $url, Html5::ATTR_ALT => $username]);

        $img        = new Contentless([], $attributes, Html5::TAG_IMG);
        $style      = sprintf('background: url(%1$s) no-repeat;', $url);
        $attributes = Attributes::fromArray([Html5::ATTR_CLASS => 'user-img', Html5::ATTR_STYLE => $style]);

        return new Tag($img, [], $attributes, Html5::TAG_DIV);
    }

    /**
     * @param string $username
     *
     * @return INode
     */
    protected function getDefaultUserImage(string $username): INode
    {
        $url        = 'https://via.placeholder.com/40/09f/fff.png';
        $attributes = Attributes::fromArray([Html5::ATTR_SRC => $url, Html5::ATTR_ALT => $username]);

        return new Contentless([], $attributes, Html5::TAG_SPAN);
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

        return TagHelper::toString($this->tag, implode("\n", $content), $this->attributes);
    }
}
