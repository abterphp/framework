<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Form\IForm;

class FormReady
{
    private IForm $form;

    /**
     * FormReady constructor.
     *
     * @param IForm $form
     */
    public function __construct(IForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return IForm
     */
    public function getForm(): IForm
    {
        return $this->form;
    }
}
