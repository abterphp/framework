<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use Opulence\Http\Requests\RequestMethods;

class Form extends Component implements IForm
{
    protected const DEFAULT_TAG = Html5::TAG_FORM;

    public const ENCTYPE_MULTIPART = 'multipart/form-data';

    /**
     * Form constructor.
     *
     * @param string      $action
     * @param string      $method
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(
        string $action,
        string $method = RequestMethods::POST,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        $attributes[Html5::ATTR_ACTION] = $action;
        $attributes[Html5::ATTR_METHOD] = $method;

        parent::__construct(null, $intents, $attributes, $tag);
    }
}
