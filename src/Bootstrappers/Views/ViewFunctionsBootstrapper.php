<?php

namespace AbterPhp\Framework\Bootstrappers\Views;

use Opulence\Framework\Views\Bootstrappers\ViewFunctionsBootstrapper as OpulenceViewFunctionsBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class ViewFunctionsBootstrapper extends OpulenceViewFunctionsBootstrapper
{
    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $this->registerCustomBindings($container);

        parent::registerBindings($container);
    }

    /**
     * @param IContainer $container
     */
    public function registerCustomBindings(IContainer $container)
    {
        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);

        $transpiler->registerViewFunction(
            'metaName',
            function (string $name, ...$contents) {
                $realContent = '';
                foreach ($contents as $content) {
                    if (empty($content)) {
                        continue;
                    }
                    $realContent = $content;
                    break;
                }

                return sprintf('<meta name="%s" content="%s">', $name, $realContent) . PHP_EOL;
            }
        );

        $transpiler->registerViewFunction(
            'metaProp',
            function (string $name, ...$contents) {
                $realContent = '';
                foreach ($contents as $content) {
                    if (empty($content)) {
                        continue;
                    }
                    $realContent = $content;
                    break;
                }

                return sprintf('<meta property="%s" content="%s">', $name, $realContent) . PHP_EOL;
            }
        );

        $transpiler->registerViewFunction(
            'author',
            function (string $author) {
                if (empty($author)) {
                    return '';
                }

                if (strlen($author) < 5) {
                    return sprintf('<meta name="author" content="%s">', $author) . PHP_EOL;
                }

                if (substr($author, 0, 8) === 'https://' || substr($author, 0, 7) === 'http://') {
                    return sprintf('<link rel="author" href="%s">', $author) . PHP_EOL;
                }

                return sprintf('<meta property="author" content="%s">', $author) . PHP_EOL;
            }
        );
    }
}
