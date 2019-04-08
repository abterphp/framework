<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\I18n;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\I18n\Translator;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class I18nBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $this->registerTranslator($container);
        $this->registerViewFunction($container);
    }

    /**
     * @param IContainer $container
     */
    private function registerTranslator(IContainer $container)
    {
        $translations = $this->getTranslations($container);

        $translator = new Translator($translations);

        $container->bindInstance(Translator::class, $translator);
        $container->bindInstance(ITranslator::class, $translator);
    }

    /**
     * @param IContainer $container
     *
     * @return array
     * @throws \Opulence\Ioc\IocException
     */
    protected function getTranslations(IContainer $container): array
    {
        $path = Config::get('paths', 'resources.lang');
        $lang = $this->getLang($container);
        $dir  = sprintf('%s/%s/', $path, $lang);

        $translations = [];
        foreach (scandir($dir) as $file) {
            if (strlen($file) < 4 || substr($file, -4) !== '.php') {
                continue;
            }

            $key   = substr($file, 0, -4);
            $value = require $dir . $file;

            $translations[$key] = $value;
        }

        return $translations;
    }

    /**
     * @param IContainer $container
     *
     * @return string
     * @throws \Opulence\Ioc\IocException
     */
    protected function getLang(IContainer $container): string
    {
        /** @var ISession $session */
        $session = $container->resolve(ISession::class);

        if ($session->has(Session::LANGUAGE_IDENTIFIER)) {
            return (string)$session->get(Session::LANGUAGE_IDENTIFIER);
        }

        return (string)getenv(Env::DEFAULT_LANGUAGE);
    }

    /**
     * @param IContainer $container
     */
    private function registerViewFunction(IContainer $container)
    {
        /** @var Translator $translator */
        $translator = $container->resolve(Translator::class);

        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);
        $transpiler->registerViewFunction(
            'tr',
            function (string $key, ...$args) use ($translator) {
                return $translator->translate($key, ...$args);
            }
        );
    }
}
