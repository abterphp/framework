<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Crypto;

use AbterPhp\Framework\Exception\Security as SecurityException;
use Exception;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\IHasher;

class Crypto
{
    protected const ERROR_INVALID_SECRET = 'packed password must be a valid SHA3-512 hash';

    protected const SECRET_HASH_LENGTH = 128;

    protected IHasher $hasher;

    protected IEncrypter $encrypter;

    protected string $pepper = '';

    /** @var array<string,string> */
    protected array $hashOptions = [];

    protected string $frontendSalt = '';

    protected string $rawSecretRegexp;

    /**
     * Authenticator constructor.
     *
     * @param IEncrypter           $encrypter
     * @param IHasher              $hasher
     * @param string               $pepper
     * @param array<string,string> $hashOptions
     * @param string               $frontendSalt
     */
    public function __construct(
        IEncrypter $encrypter,
        IHasher $hasher,
        string $pepper,
        array $hashOptions,
        string $frontendSalt
    ) {
        $this->hasher       = $hasher;
        $this->encrypter    = $encrypter;
        $this->pepper       = $pepper;
        $this->hashOptions  = $hashOptions;
        $this->frontendSalt = $frontendSalt;

        $this->rawSecretRegexp = sprintf('/^[0-9a-f]{%s}$/', static::SECRET_HASH_LENGTH);
    }

    /**
     * This method is used to "fake" frontend hashing. Use with care!
     *
     * @param string $rawText
     *
     * @return string
     */
    public function prepareSecret(string $rawText): string
    {
        return hash('sha3-512', $this->frontendSalt . $rawText);
    }

    /**
     * @param string $rawSecret SHA3-512 encoded secret in hexadecimal
     *
     * @return string secret hashed and encrypted
     * @throws SecurityException
     */
    public function hashCrypt(string $rawSecret): string
    {
        $this->assertRawSecret($rawSecret);

        try {
            $hashedSecret = $this->hasher->hash($rawSecret, $this->hashOptions, $this->pepper);

            $hashCryptedSecret = $this->encrypter->encrypt($hashedSecret);
        } catch (Exception $e) {
            throw new SecurityException($e->getMessage(), $e->getCode(), $e);
        }

        return $hashCryptedSecret;
    }

    /**
     * @param string $secret       SHA3-512 encoded secret in hexadecimal
     * @param string $storedSecret hashed and encrypted secret to compare $secret against
     *
     * @return bool
     * @throws SecurityException
     */
    public function verifySecret(string $secret, string $storedSecret): bool
    {
        try {
            $hashedSecret = $this->encrypter->decrypt($storedSecret);

            $verified = $this->hasher->verify($hashedSecret, $secret, $this->pepper);
        } catch (Exception $e) {
            throw new SecurityException($e->getMessage(), $e->getCode(), $e);
        }

        return $verified;
    }

    /**
     * @param string $secret
     */
    protected function assertRawSecret(string $secret): void
    {
        if (mb_strlen($secret) !== static::SECRET_HASH_LENGTH) {
            throw new SecurityException(static::ERROR_INVALID_SECRET);
        }

        if (!preg_match($this->rawSecretRegexp, $secret)) {
            throw new SecurityException(static::ERROR_INVALID_SECRET);
        }
    }
}
