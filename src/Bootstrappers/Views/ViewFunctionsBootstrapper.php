<?php

namespace AbterPhp\Framework\Bootstrappers\Views;

use Opulence\Framework\Views\Bootstrappers\ViewFunctionsBootstrapper as OpulenceViewFunctionsBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class ViewFunctionsBootstrapper extends OpulenceViewFunctionsBootstrapper
{
    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        $this->registerCustomBindings($container);

        parent::registerBindings($container);
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerCustomBindings(IContainer $container)
    {
        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);

        $transpiler->registerViewFunction('meta', $this->createMetaViewFunction());

        $transpiler->registerViewFunction('authorName', $this->createAuthorNameViewFunction());

        $transpiler->registerViewFunction('authorLink', $this->createAuthorLinkViewFunction());
    }

    /**
     * @return callable
     */
    public function createMetaViewFunction(): callable
    {
        return function (string $name, ...$contents) {
            $realContent = '';
            foreach ($contents as $content) {
                if (empty($content)) {
                    continue;
                }
                $realContent = $content;
                break;
            }

            return sprintf('<meta property="%s" name="%s" content="%s">', $name, $name, $realContent) . PHP_EOL;
        };
    }

    /**
     * @return callable
     */
    public function createAuthorNameViewFunction(): callable
    {
        return function (string $author) {
            if (empty($author)) {
                return '';
            }

            return sprintf('<meta property="author" name="author" content="%s">', $author) . PHP_EOL;
        };
    }

    /**
     * @return callable
     */
    public function createAuthorLinkViewFunction(): callable
    {
        return function (string $authorLink) {
            if (empty($authorLink)) {
                return '';
            }

            return sprintf('<link rel="author" href="%s">', $authorLink) . PHP_EOL;
        };
    }
}
