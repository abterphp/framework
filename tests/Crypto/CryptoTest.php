<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Crypto;

use AbterPhp\Framework\Exception\Security;
use Exception;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\IHasher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    protected const SALT         = '*?!-321-rab-oof';
    protected const PEPPER       = 'foo-bar-123-!?*';
    protected const HASH_OPTIONS = [];

    /** @var Crypto - System Under Test */
    protected Crypto $sut;

    /** @var IEncrypter|MockObject */
    protected $encrypterMock;

    /** @var IHasher|MockObject */
    protected $hasherMock;

    public function setUp(): void
    {
        $this->encrypterMock = $this->createMock(IEncrypter::class);

        $this->hasherMock = $this->createMock(IHasher::class);

        $this->sut = new Crypto(
            $this->encrypterMock,
            $this->hasherMock,
            static::PEPPER,
            static::HASH_OPTIONS,
            static::SALT
        );

        parent::setUp();
    }

    public function testPrepareSecret(): void
    {
        $actualResult = $this->sut->prepareSecret('sha-512');

        $this->assertEquals(128, strlen($actualResult));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{128}$/', $actualResult);
    }

    public function testHashCryptHashesAndEncryptsSecret(): void
    {
        $secret         = str_repeat('f00ba788', 16);
        $hashedSecret   = 'bar';
        $expectedResult = 'baz';

        $this->hasherMock
            ->expects($this->once())
            ->method('hash')
            ->with($secret, static::HASH_OPTIONS, static::PEPPER)
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
    public function testHashCryptThrowsSecurityExceptionIfSecretIsInvalid(string $secret): void
    {
        $this->expectException(Security::class);

        $this->hasherMock->expects($this->never())->method('hash');
        $this->encrypterMock->expects($this->never())->method('encrypt');

        $this->sut->hashCrypt($secret);
    }

    public function testHashCryptThrowsSecurityExceptionIfHasherThrowsException(): void
    {
        $this->expectException(Security::class);

        $secret = str_repeat('f00ba788', 16);

        $this->hasherMock->expects($this->once())->method('hash')->willThrowException(new Exception());
        $this->encrypterMock->expects($this->never())->method('encrypt');

        $this->sut->hashCrypt($secret);
    }

    public function testHashCryptThrowsSecurityExceptionIfEncrypterThrowsException(): void
    {
        $this->expectException(Security::class);

        $secret       = str_repeat('f00ba788', 16);
        $hashedSecret = 'bar';

        $this->hasherMock->expects($this->any())->method('hash')->willReturn($hashedSecret);
        $this->encrypterMock->expects($this->once())->method('encrypt')->willThrowException(new Exception());

        $this->sut->hashCrypt($secret);
    }

    public function testVerifySecretThrowsSecurityExceptionIfEncrypterThrowsException(): void
    {
        $this->markTestIncomplete('Hasher::verify is static...');
    }

    public function testVerifySecretThrowsSecurityExceptionIfHasherThrowsException(): void
    {
        $this->markTestIncomplete('Hasher::verify is static...');
    }

    public function testVerifySecret(): void
    {
        $this->markTestIncomplete('Hasher::verify is static...');
    }
}
