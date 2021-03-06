<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Security;

use Opulence\Console\Responses\IResponse;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class SecretGeneratorTest extends TestCase
{
    protected const ROOT = 'exampleDir';
    protected const CONFIG = 'exampleDir/config.php';
    protected const CONFIG_EXTRA_KEY = 'MY_RANDOM_PASSWORD';
    protected const CONFIG_CONTENT = <<<EOF
        <?php
        Environment::setVar("DB_PASSWORD", "mypassword");
        Environment::setVar("ENCRYPTION_KEY", "mypassword");
        Environment::setVar("CRYPTO_FRONTEND_SALT", "mypassword");
        Environment::setVar("CRYPTO_ENCRYPTION_PEPPER", "mypassword");
        Environment::setVar("OAUTH2_PRIVATE_KEY_PASSWORD", "mypassword");
        Environment::setVar("MY_RANDOM_PASSWORD", "mypassword");
        EOF;

    /** @var SecretGenerator - System Under Test */
    protected SecretGenerator $sut;

    protected string $configUrl;

    public function setUp(): void
    {
        vfsStream::setup(self::ROOT);
        $this->configUrl = vfsStream::url(self::CONFIG);

        $this->sut = new SecretGenerator();
        $this->sut->setEnvFile($this->configUrl);

        file_put_contents($this->configUrl, self::CONFIG_CONTENT);
    }

    public function testExecuteThrowsExceptionIfConfigIsNotFoundAndNotInDryRunMode(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->sut->setEnvFile(null);

        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->execute($responseMock);
    }

    public function testExecuteCreatesSixRandomStringsByDefault(): void
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->exactly(6))->method('writeln');

        $this->sut->execute($responseMock);
    }

    public function testExecuteCreatesAdditionalRandomStringsForAdditionalKeys(): void
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->exactly(7))->method('writeln');

        $this->sut->addKey('foo', 32);

        $this->sut->execute($responseMock);
    }

    public function testExecuteReplacesConfigValues(): void
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->addKey(self::CONFIG_EXTRA_KEY, 32);

        $this->sut->execute($responseMock);
        $content = file_get_contents($this->configUrl);

        $this->assertNotSame(self::CONFIG_CONTENT, $content);
        $this->assertStringNotContainsString('mypassword', $content);
    }

    public function testExecuteCanReplaceFilesMultipleTimes(): void
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->addKey(self::CONFIG_EXTRA_KEY, 32);

        $this->sut->execute($responseMock);
        $content1 = file_get_contents($this->configUrl);

        $this->sut->execute($responseMock);
        $content2 = file_get_contents($this->configUrl);

        $this->assertNotSame($content1, $content2);
    }
}
