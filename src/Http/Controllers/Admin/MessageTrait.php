<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Admin;

use AbterPhp\Framework\I18n\ITranslator;

trait MessageTrait
{
    /**
     * @param string $messageType
     *
     * @return string
     */
    protected function getMessage(string $messageType)
    {
        /** @var ITranslator $translator */
        $translator = $this->translator;

        $entityName = $translator->translate(static::ENTITY_TITLE_SINGULAR);

        return $translator->translate($messageType, $entityName);
    }
}
