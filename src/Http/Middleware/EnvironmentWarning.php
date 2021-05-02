<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\Html\Helper\Css;
use AbterPhp\Framework\I18n\ITranslator;
use Closure;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;

class EnvironmentWarning implements IMiddleware
{
    protected ITranslator $translator;

    protected string $environment;

    /**
     * EnvironmentWarning constructor.
     *
     * @param ITranslator $translator
     * @param string      $environment
     */
    public function __construct(ITranslator $translator, string $environment)
    {
        $this->translator  = $translator;
        $this->environment = $environment;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($this->environment == Environment::PRODUCTION) {
            return $response;
        }

        $warning = $this->getWarningHtml($this->environment);

        $response->setContent(preg_replace('/<body([^>]*)>/', '<body${1}>' . $warning, $response->getContent(), 1));

        return $response;
    }

    /**
     * @param string $environmentName
     *
     * @return string
     */
    protected function getWarningHtml(string $environmentName): string
    {
        $styles  = [
            'color'       => 'white',
            'line-height' => '1em',
            'font-weight' => 'bold',
        ];
        $warning = sprintf(
            '<p style="%s">%s</p>',
            Css::toStyles($styles),
            $this->translator->translate('admin:environment', $environmentName)
        );

        $styles  = [
            'position'   => 'fixed',
            'bottom'     => '10px',
            'right'      => '10px',
            'z-index'    => '10000',
            'padding'    => '1em 1em 0.5em',
            'margin'     => '0 auto',
            'background' => '#ff5722',
            'cursor'     => 'pointer',
        ];
        $onClick = '$(this).remove()';

        return sprintf('<div style="%s" onclick="%s">%s</div>', Css::toStyles($styles), $onClick, $warning);
    }
}
