<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Session;

use AbterPhp\Framework\Session\Helper\ArrayHelper;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Sessions\ISession;

class FlashService
{
    protected const ERROR   = 'error';
    protected const SUCCESS = 'success';

    /** @var ISession */
    protected $session;

    /** @var ITranslator */
    protected $translator;

    /**
     * Helper constructor.
     *
     * @param ISession    $session
     * @param ITranslator $translator
     */
    public function __construct(ISession $session, ITranslator $translator)
    {
        $this->session    = $session;
        $this->translator = $translator;
    }

    /**
     * @param string[] $messages
     */
    public function mergeSuccessMessages(array $messages)
    {
        $currentMessages = (array)$this->session->get(static::SUCCESS);

        $newMessages = array_merge($currentMessages, $messages);

        $this->session->flash(static::SUCCESS, $newMessages);
    }

    /**
     * @param array  $messages
     */
    public function mergeErrorMessages(array $messages)
    {
        $messages = ArrayHelper::flatten($messages);

        $currentMessages = (array)$this->session->get(static::ERROR);

        $newMessages = array_merge($currentMessages, $messages);

        $this->session->flash(static::ERROR, $newMessages);
    }

    /**
     * @return array
     */
    public function retrieveSuccessMessages()
    {
        return (array)$this->session->get(static::SUCCESS);
    }

    /**
     * @return array
     */
    public function retrieveErrorMessages()
    {
        return (array)$this->session->get(static::ERROR);
    }
}
