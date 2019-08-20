<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Crypto;

use AbterPhp\Framework\Exception\Security;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\IHasher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    /** @var Crypto - System Under Test */
    protected $sut;

    /** @var IEncrypter|MockObject */
    protected $encrypterMock;

    /** @var IHasher|MockObject */
    protected $hasherMock;

    /** @var string */
    protected $pepper = 'foo-bar-123-!?*';

    /** @var array */
    protected $hashOptions = [];

    /** @var string */
    protected $salt = '*?!-321-rab-oof';

    public function setUp(): void
    {
        $this->encrypterMock = $this->createMock(IEncrypter::class);

        $this->hasherMock = $this->createMock(IHasher::class);

        $this->sut = new Crypto(
            $this->encrypterMock,
            $this->hasherMock,
            $this->pepper,
            $this->hashOptions,
            $this->salt
        );

        parent::setUp();
    }

    public function testPrepareSecret()
    {
        $actualResult = $this->sut->prepareSecret('sha-512');

        $this->assertEquals(Crypto::SECRET_HASH_LENGTH, strlen($actualResult));
        $this->assertRegExp('/^[0-9a-f]{128}$/', $actualResult);
    }

    public function testHashCryptHashesAndEncryptsSecret()
    {
        $secret         = str_repeat('f00ba788', 16);
        $hashedSecret   = 'bar';
        $expectedResult = 'baz';

        $this->hasherMock
            ->expects($this->once())
            ->method('hash')
            ->with($secret, $this->hashOptions, $this->pepper)
            ->willReturn($hashedSecret);

        $this->encrypterMock->expects($this->once())->method('encrypt')->with($hashedSecret)->willReturn(
            $expectedResult
        );

        $actualResult = $this->sut->hashCrypt($secret);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function hashCryptThrowsExceptionIfSecretIsInvalidProvider(): array
    {
        return [
            ['foo'],
            [str_repeat('foobar  ', 16)],
        ];
    }

    /**
     * @dataProvider hashCryptThrowsExceptionIfSecretIsInvalidProvider
     *
     * @param string $secret
     */
    public function testHashCryptThrowsSecurityExceptionIfSecretIsInvalid($secret)
    {
        $this->expectException(Security::class);

        $this->hasherMock->expects($this->never())->method('hash');
        $this->encrypterMock->expects($this->never())->method('encrypt');

        $this->sut->hashCrypt($secret);
    }

    public function testHashCryptThrowsSecurityExceptionIfHasherThrowsException()
    {
        $this->expectException(Security::class);

        $secret = str_repeat('f00ba788', 16);

        $this->hasherMock->expects($this->once())->method('hash')->willThrowException(new \Exception());
        $this->encrypterMock->expects($this->never())->method('encrypt');

        $this->sut->hashCrypt($secret);
    }

    public function testHashCryptThrowsSecurityExceptionIfEncrypterThrowsException()
    {
        $this->expectException(Security::class);

        $secret       = str_repeat('f00ba788', 16);
        $hashedSecret = 'bar';

        $this->hasherMock->expects($this->any())->method('hash')->willReturn($hashedSecret);
        $this->encrypterMock->expects($this->once())->method('encrypt')->willThrowException(new \Exception());

        $this->sut->hashCrypt($secret);
    }

    public function testVerifySecretThrowsSecurityExceptionIfEncrypterThrowsException()
    {
        $this->markTestIncomplete('Hasher::verify is static...');
    }

    public function testVerifySecretThrowsSecurityExceptionIfHasherThrowsException()
    {
        $this->markTestIncomplete('Hasher::verify is static...');
    }

    public function testVerifySecret()
    {
        $this->markTestIncomplete('Hasher::verify is static...');
    }
}
