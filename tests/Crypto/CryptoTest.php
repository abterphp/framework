<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Crypto;

use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\IHasher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    /** @var Crypto */
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

    public function setUp()
    {
        $this->encrypterMock = $this->getMockBuilder(IEncrypter::class)
            ->setMethods(['encrypt', 'decrypt', 'setSecret'])
            ->getMock();

        $this->hasherMock = $this->getMockBuilder(IHasher::class)
            ->setMethods(['hash', 'verify', 'needsRehash'])
            ->getMock();

        $this->sut = new Crypto(
            $this->encrypterMock,
            $this->hasherMock,
            $this->pepper,
            $this->hashOptions,
            $this->salt
        );
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
     * @expectedException \AbterPhp\Framework\Security\SecurityException
     *
     * @param string $secret
     */
    public function testHashCryptThrowsSecurityExceptionIfSecretIsInvalid($secret)
    {
        $this->hasherMock->expects($this->never())->method('hash');
        $this->encrypterMock->expects($this->never())->method('encrypt');

        $this->sut->hashCrypt($secret);
    }

    /**
     * @expectedException \AbterPhp\Framework\Security\SecurityException
     */
    public function testHashCryptThrowsSecurityExceptionIfHasherThrowsException()
    {
        $secret = str_repeat('f00ba788', 16);

        $this->hasherMock->expects($this->once())->method('hash')->willThrowException(new \Exception());
        $this->encrypterMock->expects($this->never())->method('encrypt');

        $this->sut->hashCrypt($secret);
    }

    /**
     * @expectedException \AbterPhp\Framework\Security\SecurityException
     */
    public function testHashCryptThrowsSecurityExceptionIfEncrypterThrowsException()
    {
        $secret       = str_repeat('f00ba788', 16);
        $hashedSecret = 'bar';

        $this->hasherMock->expects($this->any())->method('hash')->willReturn($hashedSecret);
        $this->encrypterMock->expects($this->once())->method('encrypt')->willThrowException(new \Exception());

        $this->sut->hashCrypt($secret);
    }

    /**
     * @expectedException \AbterPhp\Framework\Security\SecurityException
     */
    public function testVerifySecretThrowsSecurityExceptionIfEncrypterThrowsException()
    {
        $this->markTestSkipped('Hasher::verify is static...');
    }

    /**
     * @expectedException \AbterPhp\Framework\Security\SecurityException
     */
    public function testVerifySecretThrowsSecurityExceptionIfHasherThrowsException()
    {
        $this->markTestSkipped('Hasher::verify is static...');
    }

    public function testVerifySecret()
    {
        $this->markTestSkipped('Hasher::verify is static...');
    }
}
