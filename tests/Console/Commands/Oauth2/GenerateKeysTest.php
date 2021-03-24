<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Oauth2;

use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\TestCase;

class GenerateKeysTest extends TestCase
{
    protected const PRIVATE_KEY_PASS = 'foo-pass';
    protected const PRIVATE_KEY_PATH = '/tmp/private';
    protected const PUBLIC_KEY_PATH  = '/tmp/public';

    /** @var GenerateKeys - System Under Test */
    protected GenerateKeys $sut;

    public function setUp(): void
    {
        $this->sut = new GenerateKeys(static::PRIVATE_KEY_PASS, static::PRIVATE_KEY_PATH, static::PUBLIC_KEY_PATH);
    }

    public function testExecuteWritesFatalIfOpenSslIsNotAvailable(): void
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())
            ->method('writeln')
            ->with('<fatal>OpenSSL is not installed.</fatal>');

        $this->sut->setIsOpenSslAvailable(false);

        $this->sut->execute($responseMock);
    }

    public function testExecuteCreatesOpenSslKeys(): void
    {
        if (!defined('OPENSSL_VERSION_NUMBER')) {
            $this->markTestSkipped('no openssl installed');
        }

        if (!is_writable(dirname(static::PRIVATE_KEY_PATH)) || !is_writable(dirname(static::PUBLIC_KEY_PATH))) {
            $this->markTestSkipped('private or public key path are not writable');
        }

        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->execute($responseMock);
        $this->assertFileExists(static::PRIVATE_KEY_PATH);
        $this->assertFileExists(static::PUBLIC_KEY_PATH);
    }
}
