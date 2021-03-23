<?php

namespace AbterPhp\Framework\Bootstrappers\Crypto;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Crypto\Crypto;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\BcryptHasher;
use Opulence\Cryptography\Hashing\IHasher;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class CryptoBootstrapperTest extends TestCase
{
    /** @var CryptoBootstrapper - System Under Test */
    protected CryptoBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new CryptoBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::CRYPTO_ENCRYPTION_PEPPER);
        Environment::unsetVar(Env::CRYPTO_FRONTEND_SALT);
        Environment::unsetVar(Env::CRYPTO_BCRYPT_SALT);
        Environment::unsetVar(Env::CRYPTO_BCRYPT_COST);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::CRYPTO_ENCRYPTION_PEPPER, 'foo');
        Environment::setVar(Env::CRYPTO_FRONTEND_SALT, 'bar');

        $encrypterMock = $this->getMockBuilder(IEncrypter::class)->disableOriginalConstructor()->getMock();
        $hasherMock    = $this->getMockBuilder(IHasher::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(IEncrypter::class, $encrypterMock);
        $container->bindInstance(IHasher::class, $hasherMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Crypto::class);
        $this->assertInstanceOf(Crypto::class, $actual);
    }

    public function testRegisterBindingsBcrypt()
    {
        Environment::setVar(Env::CRYPTO_ENCRYPTION_PEPPER, 'foo');
        Environment::setVar(Env::CRYPTO_FRONTEND_SALT, 'bar');
        Environment::setVar(Env::CRYPTO_BCRYPT_SALT, 'baz');
        Environment::setVar(Env::CRYPTO_BCRYPT_COST, 'quix');

        $encrypterMock = $this->getMockBuilder(IEncrypter::class)->disableOriginalConstructor()->getMock();
        $hasherMock    = $this->getMockBuilder(BcryptHasher::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(IEncrypter::class, $encrypterMock);
        $container->bindInstance(IHasher::class, $hasherMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Crypto::class);
        $this->assertInstanceOf(Crypto::class, $actual);
    }
}
