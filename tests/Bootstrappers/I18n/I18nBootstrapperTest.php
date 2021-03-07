<?php

namespace AbterPhp\Framework\Bootstrappers\I18n;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\I18n\Translator;
use Opulence\Ioc\Container;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class I18nBootstrapperTest extends TestCase
{
    private const BASE_PATH = 'exampleDir';

    /** @var I18nBootstrapper */
    protected I18nBootstrapper $sut;

    protected vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->root = vfsStream::setup(static::BASE_PATH);

        $this->sut = new I18nBootstrapper();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::DEFAULT_LANGUAGE);
    }

    public function testRegisterBindings()
    {
        $langStub = 'en';

        Environment::setVar(Env::DEFAULT_LANGUAGE, $langStub);

        mkdir(vfsStream::url(static::BASE_PATH) . '/lang/en', 0777, true);
        file_put_contents(vfsStream::url(static::BASE_PATH) . '/lang/en/.gitignore', '');
        file_put_contents(vfsStream::url(static::BASE_PATH) . '/lang/en/foo.php', '<?php return ["bar" => "baz"];');

        $sessionMock = $this->getMockBuilder(ISession::class)->getMock();
        $sessionMock->expects($this->once())->method('has')->with(Session::LANGUAGE_IDENTIFIER)->willReturn(true);
        $sessionMock->expects($this->once())->method('get')->with(Session::LANGUAGE_IDENTIFIER)->willReturn($langStub);
        $translatorMock = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
        $transpilerMock = $this->getMockBuilder(ITranspiler::class)->getMock();

        $container = new Container();
        $container->bindInstance(ISession::class, $sessionMock);
        $container->bindInstance(Translator::class, $translatorMock);
        $container->bindInstance(ITranspiler::class, $transpilerMock);

        $resourcePaths = [vfsStream::url(static::BASE_PATH), '/tmp/dir-does-not-exist-ever-299182'];

        $this->sut->setResourcePaths($resourcePaths);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Translator::class);
        $this->assertInstanceOf(Translator::class, $actual);

        /** @var Translator $actual */
        $actual = $container->resolve(ITranslator::class);
        $this->assertInstanceOf(Translator::class, $actual);

        $this->assertSame($actual->translate('foo:bar'), 'baz');
    }
}
