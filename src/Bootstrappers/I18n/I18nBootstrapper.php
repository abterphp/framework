<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\I18n;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\I18n\Translator;
use Opulence\Environments\Environment;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class I18nBootstrapper extends Bootstrapper
{
    protected const LANG_PATH = 'lang';

    protected ?array $resourcePaths = null;

    /**
     * @return array
     */
    public function getResourcePaths(): array
    {
        global $abterModuleManager;

        if ($this->resourcePaths !== null) {
            return $this->resourcePaths;
        }

        $this->resourcePaths = $abterModuleManager->getResourcePaths() ?: [];

        return $this->resourcePaths;
    }

    /**
     * @param array $resourcePaths
     *
     * @return $this
     */
    public function setResourcePaths(array $resourcePaths): self
    {
        $this->resourcePaths = $resourcePaths;

        return $this;
    }

    /**
     * @inheritdoc
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        $this->registerTranslator($container);
        $this->registerViewFunction($container);
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    private function registerTranslator(IContainer $container)
    {
        $lang = $this->getLang($container);

        $translations = $this->getTranslations($lang);

        $translator = new Translator($translations);

        $container->bindInstance(Translator::class, $translator);
        $container->bindInstance(ITranslator::class, $translator);
    }

    /**
     * @param IContainer $container
     *
     * @return string
     * @throws IocException
     */
    protected function getLang(IContainer $container): string
    {
        /** @var ISession $session */
        $session = $container->resolve(ISession::class);

        if ($session->has(Session::LANGUAGE_IDENTIFIER)) {
            return (string)$session->get(Session::LANGUAGE_IDENTIFIER);
        }

        return (string)Environment::getVar(Env::DEFAULT_LANGUAGE);
    }

    /**
     * @param string $lang
     *
     * @return array
     */
    protected function getTranslations(string $lang): array
    {
        $translations = [];
        foreach ($this->getLangDirs($lang) as $dir) {
            $translations = array_merge($translations, $this->scanDir($dir));
        }

        return $translations;
    }

    /**
     * @param string $dir
     *
     * @return string[]
     */
    protected function scanDir(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $translations = [];
        foreach (scandir($dir) as $file) {
            if (strlen($file) < 4 || substr($file, -4) !== '.php') {
                continue;
            }

            $key   = substr($file, 0, -4);
            $value = require rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;

            $translations[$key] = $value;
        }

        return $translations;
    }

    /**
     * @param string $lang
     *
     * @return string[]
     */
    protected function getLangDirs(string $lang): array
    {
        $paths = [];

        $globalPath = Config::get('paths', 'resources.lang');
        if ($globalPath) {
            $paths[] = sprintf('%s/%s/', $globalPath, $lang);
        }

        foreach ($this->getResourcePaths() as $path) {
            $a = rtrim($path, DIRECTORY_SEPARATOR);
            $b = static::LANG_PATH;
            $c = rtrim($lang, DIRECTORY_SEPARATOR);

            $paths[] = $a . DIRECTORY_SEPARATOR . $b . DIRECTORY_SEPARATOR . $c;
        }

        return $paths;
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
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
