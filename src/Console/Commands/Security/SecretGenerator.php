<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace AbterPhp\Framework\Console\Commands\Security;

use AbterPhp\Framework\Constant\Env;
use Defuse\Crypto\Key;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the encryption key generator command
 */
class SecretGenerator extends Command
{
    /** @var array */
    protected $keys = [
        Env::DB_PASSWORD                 => 12,
        Env::ENCRYPTION_KEY              => 32,
        Env::CRYPTO_FRONTEND_SALT        => 8,
        Env::CRYPTO_ENCRYPTION_PEPPER    => 16,
        Env::OAUTH2_PRIVATE_KEY_PASSWORD => 16,
    ];

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('abterphp:generatesecrets')
            ->setDescription('Creates secrets for AbterAdmin')
            ->addOption(
                new Option(
                    'show',
                    's',
                    OptionTypes::NO_VALUE,
                    'Whether to just show the new secrets or replace them in the environment config'
                )
            );
    }

    /**
     * @param string $name
     * @param int    $length
     */
    public function addKey(string $name, int $length)
    {
        $this->keys[$name] = $length;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $maxNameLength = strlen(Env::OAUTH2_ENCRYPTION_KEY);
        foreach ($this->keys as $name => $length) {
            $maxNameLength = (int)max($maxNameLength, strlen($name));
        }

        foreach ($this->keys as $name => $length) {
            $key = \bin2hex(\random_bytes($length));
            $this->handleKey($response, $name, $key, $maxNameLength);
        }

        $key = Key::createNewRandomKey()->saveToAsciiSafeString();
        $this->handleOauth2EncryptionKey($response, Env::OAUTH2_ENCRYPTION_KEY, $key, $maxNameLength);
    }

    /**
     * @param IResponse $response
     * @param string    $name
     * @param string    $key
     * @param int       $maxNameLength
     *
     * @throws \Exception
     */
    protected function handleKey(IResponse $response, string $name, string $key, int $maxNameLength)
    {
        $envConfigPath = Config::get('paths', 'config') . '/environment/.env.app.php';

        if (!$this->optionIsSet('show') && file_exists($envConfigPath)) {
            $contents    = file_get_contents($envConfigPath);
            $newContents = preg_replace(
                sprintf("/\"%s\",\s*\"[^\"]*\"/U", $name),
                sprintf('"%s", "' . $key . '"', $name),
                $contents
            );
            file_put_contents($envConfigPath, $newContents);
        }

        $pad = str_repeat(' ', $maxNameLength - strlen($name));
        $response->writeln("Generated $name:$pad <info>$key</info>");
    }
}
