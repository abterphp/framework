<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Oauth2;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class GenerateKeys extends Command
{
    const NAME = 'oauth2:generatekeys';

    /** @var string */
    protected $privateKeyPassword;

    /** @var string */
    protected $privateKeyPath;

    /** @var string */
    protected $publicKeyPath;

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

        parent::__construct();
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
        $genPrivateKeyCmd = sprintf(
            'openssl genrsa -passout pass:%s -out %s 2048',
            $this->privateKeyPassword,
            $this->privateKeyPath
        );
        $response->writeln(sprintf('<comment>%s</comment>', $genPrivateKeyCmd));
        exec($genPrivateKeyCmd);

        $genPublicKeyCmd = sprintf(
            'openssl rsa -in %s -passin pass:%s -pubout -out %s',
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
