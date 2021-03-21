<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Oauth2;

use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\TestCase;

class GenerateKeysTest extends TestCase
{
    private GenerateKeys $sut;

    /** @var string */
    private $privateKeyPassword = 'foopass';

    /** @var string */
    private $privateKeyPath = '/tmp/private';

    /** @var string */
    private $publicKeyPath = '/tmp/public';

    public function setUp(): void
    {
        $this->sut = new GenerateKeys($this->privateKeyPassword, $this->privateKeyPath, $this->publicKeyPath);
    }

    public function testExecuteWritesFatalIfOpenSslIsNotAvailable()
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

    public function testExecuteCreatesOpenSslKeys()
    {
        if (!defined('OPENSSL_VERSION_NUMBER')) {
            $this->markTestSkipped('no openssl installed');
        }

        if (!is_writable(dirname($this->privateKeyPath)) || !is_writable(dirname($this->publicKeyPath))) {
            $this->markTestSkipped('private or public key path are not writable');
        }

        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->execute($responseMock);
        $this->assertFileExists($this->privateKeyPath);
        $this->assertFileExists($this->publicKeyPath);
    }
}
