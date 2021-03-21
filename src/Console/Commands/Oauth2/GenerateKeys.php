<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Oauth2;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class GenerateKeys extends Command
{
    public const NAME = 'oauth2:generatekeys';

    protected string $privateKeyPassword;

    protected string $privateKeyPath;

    protected string $publicKeyPath;

    protected bool $isOpenSslAvailable = true;

    /**
     * GenerateKeys constructor.
     *
     * @param string $privateKeyPassword
     * @param string $privateKeyPath
     * @param string $publicKeyPath
     */
    public function __construct(string $privateKeyPassword, string $privateKeyPath, string $publicKeyPath)
    {
        $this->privateKeyPassword = $privateKeyPassword;
        $this->privateKeyPath     = $privateKeyPath;
        $this->publicKeyPath      = $publicKeyPath;

        if (!defined('OPENSSL_VERSION_NUMBER')) {
            $this->isOpenSslAvailable = false;
        }

        parent::__construct();
    }

    /**
     * @param bool $isOpenSslAvailable
     *
     * @return $this
     */
    public function setIsOpenSslAvailable(bool $isOpenSslAvailable): self
    {
        $this->isOpenSslAvailable = $isOpenSslAvailable;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName(static::NAME)
            ->setDescription('Generates openssl keys');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        if (!$this->isOpenSslAvailable) {
            $response->writeln('<fatal>OpenSSL is not installed.</fatal>');

            return;
        }

        $genPrivateKeyCmd = sprintf(
            'openssl genrsa -passout pass:%s -out %s 2048 2> /dev/null',
            $this->privateKeyPassword,
            $this->privateKeyPath
        );
        $response->writeln(sprintf('<comment>%s</comment>', $genPrivateKeyCmd));
        exec($genPrivateKeyCmd);

        $genPublicKeyCmd = sprintf(
            'openssl rsa -in %s -passin pass:%s -pubout -out %s 2> /dev/null',
            $this->privateKeyPath,
            $this->privateKeyPassword,
            $this->publicKeyPath
        );
        $response->writeln(sprintf('<comment>%s</comment>', $genPublicKeyCmd));
        exec($genPublicKeyCmd);

        chmod($this->privateKeyPath, 0600);
        chmod($this->publicKeyPath, 0600);
    }
}
