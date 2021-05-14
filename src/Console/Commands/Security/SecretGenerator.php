<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Security;

use AbterPhp\Framework\Constant\Env;
use Defuse\Crypto\Key;
use Exception;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Configuration\Config;

class SecretGenerator extends Command
{
    /** @var array<string,int> */
    protected array $keys = [
        Env::DB_PASSWORD                 => 12,
        Env::ENCRYPTION_KEY              => 32,
        Env::CRYPTO_FRONTEND_SALT        => 8,
        Env::CRYPTO_ENCRYPTION_PEPPER    => 16,
        Env::OAUTH2_PRIVATE_KEY_PASSWORD => 16,
    ];

    /** @var null|string */
    protected ?string $envFile = null;

    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        $this->setName('abterphp:generatesecrets')
            ->setDescription('Creates secrets for AbterAdmin')
            ->addOption(
                new Option(
                    'dry-run',
                    'd',
                    OptionTypes::NO_VALUE,
                    'Whether to just show the new secrets or also replace them in the environment config'
                )
            );
    }

    /**
     * @param string $name
     * @param int    $length
     */
    public function addKey(string $name, int $length): void
    {
        $this->keys[$name] = $length;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function doExecute(IResponse $response): void
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
        $this->handleKey($response, Env::OAUTH2_ENCRYPTION_KEY, $key, $maxNameLength);
    }

    /**
     * @return string|null
     */
    protected function getEnvFile(): ?string
    {
        if (null !== $this->envFile) {
            return $this->envFile;
        }

        $fileName = Config::get('paths', 'config') . '/environment/.env.app.php';

        if (!file_exists($fileName)) {
            throw new \RuntimeException('app config not found: ' . $fileName);
        }

        $this->envFile = $fileName;

        return $this->envFile;
    }

    /**
     * @param string|null $envFile
     *
     * @return $this
     */
    public function setEnvFile(?string $envFile = null): self
    {
        $this->envFile = $envFile;

        return $this;
    }

    /**
     * @param IResponse $response
     * @param string    $name
     * @param string    $key
     * @param int       $maxNameLength
     *
     * @throws Exception
     */
    protected function handleKey(IResponse $response, string $name, string $key, int $maxNameLength): void
    {
        if (!$this->optionIsSet('dry-run') && $this->getEnvFile()) {
            $contents    = file_get_contents($this->getEnvFile());
            $newContents = preg_replace(
                sprintf("/\"%s\",\s*\"[^\"]*\"/U", $name),
                sprintf('"%s", "' . $key . '"', $name),
                $contents
            );
            file_put_contents($this->getEnvFile(), $newContents);
        }

        $pad = str_repeat(' ', $maxNameLength - strlen($name));
        $response->writeln("Generated $name:$pad <info>$key</info>");
    }
}
