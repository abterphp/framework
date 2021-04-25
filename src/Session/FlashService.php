<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Session;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\Helper\ArrayHelper;
use Opulence\Sessions\ISession;

class FlashService
{
    protected const ERROR   = 'error';
    protected const SUCCESS = 'success';

    protected ISession $session;

    protected ?ITranslator $translator;

    /**
     * Helper constructor.
     *
     * @param ISession         $session
     * @param ITranslator|null $translator
     */
    public function __construct(ISession $session, ?ITranslator $translator)
    {
        $this->session    = $session;
        $this->translator = $translator;
    }

    /**
     * @param string[] $messages
     */
    public function mergeSuccessMessages(array $messages): void
    {
        $currentMessages = (array)$this->session->get(static::SUCCESS);

        $newMessages = array_merge($currentMessages, $messages);

        $this->session->flash(static::SUCCESS, $newMessages);
    }

    /**
     * @param array $messages
     */
    public function mergeErrorMessages(array $messages): void
    {
        $messages = ArrayHelper::flatten($messages);

        $currentMessages = (array)$this->session->get(static::ERROR);

        $newMessages = array_merge($currentMessages, $messages);

        $this->session->flash(static::ERROR, $newMessages);
    }

    /**
     * @return array
     */
    public function retrieveSuccessMessages(): array
    {
        return (array)$this->session->get(static::SUCCESS);
    }

    /**
     * @return array
     */
    public function retrieveErrorMessages(): array
    {
        return (array)$this->session->get(static::ERROR);
    }
}
