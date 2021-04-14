<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Crypto;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Crypto\Crypto;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\BcryptHasher;
use Opulence\Cryptography\Hashing\IHasher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class CryptoBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            Crypto::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        $encrypter = $container->resolve(IEncrypter::class);
        $hasher    = $container->resolve(IHasher::class);

        $pepper      = $this->getPepper();
        $hashOptions = $this->getHashOptions($hasher);
        $salt        = $this->getSalt();

        $authenticator = new Crypto($encrypter, $hasher, $pepper, $hashOptions, $salt);

        $container->bindInstance(Crypto::class, $authenticator);
    }

    /**
     * @return string
     */
    private function getPepper(): string
    {
        return Environment::mustGetVar(Env::CRYPTO_ENCRYPTION_PEPPER);
    }

    /**
     * @return string
     */
    private function getSalt(): string
    {
        return Environment::mustGetVar(Env::CRYPTO_FRONTEND_SALT);
    }

    /**
     * @param IHasher $hasher
     *
     * @return array<string,string>
     */
    private function getHashOptions(IHasher $hasher): array
    {
        $options = [];
        if ($hasher instanceof BcryptHasher) {
            $salt = Environment::getVar(Env::CRYPTO_BCRYPT_SALT);
            if ($salt) {
                $options['salt'] = $salt;
            }

            $cost = Environment::getVar(Env::CRYPTO_BCRYPT_COST);
            if ($cost) {
                $options['cost'] = $cost;
            }
        }

        return $options;
    }
}
