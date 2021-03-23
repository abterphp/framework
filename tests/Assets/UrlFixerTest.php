<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use PHPUnit\Framework\TestCase;

class UrlFixerTest extends TestCase
{
    protected const CACHE_URL = 'xxx/www';

    /** @var UrlFixer - System Under Test */
    protected UrlFixer $sut;

    public function setUp(): void
    {
        $this->sut = new UrlFixer(self::CACHE_URL);
    }

    /**
     * @return array
     */
    public function fixCssProvider(): array
    {
        return [
            'empty'                                   => ['', 'yyy/zzz.css', ''],
            'empty-url'                               => ['url()', 'yyy/zzz.css', 'url()'],
            '1ch-url'                                 => ['url(a)', 'yyy/zzz.css', 'url(a)'],
            '2ch-url'                                 => ['url(ab)', 'yyy/zzz.css', 'url(xxx/www/yyy/ab)'],
            'data'                                    => [
                'url(data:/fonts/roboto/Roboto-Regular-webfont.eot?xasd)',
                'yyy/zzz.css',
                "url(data:/fonts/roboto/Roboto-Regular-webfont.eot?xasd)",
            ],
            'url'                                     => [
                'url(abc://fonts/roboto/Roboto-Regular-webfont.eot?xasd)',
                'yyy/zzz.css',
                'url(abc://fonts/roboto/Roboto-Regular-webfont.eot?xasd)',
            ],
            //
            'relative-implicit-current-simple-quotes' => [
                "url('fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
                'yyy/zzz.css',
                "url('xxx/www/yyy/fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
            ],
            'relative-implicit-current-double-quotes' => [
                'url("fonts/roboto/Roboto-Regular-webfont.eot?xasd")',
                'yyy/zzz.css',
                'url("xxx/www/yyy/fonts/roboto/Roboto-Regular-webfont.eot?xasd")',
            ],
            'relative-implicit-current-no-quotes'     => [
                "url(fonts/roboto/Roboto-Regular-webfont.eot?xasd)",
                'yyy/zzz.css',
                "url(xxx/www/yyy/fonts/roboto/Roboto-Regular-webfont.eot?xasd)",
            ],
            //
            'relative-explicit-current-simple-quotes' => [
                "url('./fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
                'yyy/zzz.css',
                "url('xxx/www/yyy/fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
            ],
            'relative-explicit-current-double-quotes' => [
                'url("./fonts/roboto/Roboto-Regular-webfont.eot?xasd")',
                'yyy/zzz.css',
                'url("xxx/www/yyy/fonts/roboto/Roboto-Regular-webfont.eot?xasd")',
            ],
            'relative-explicit-current-no-quotes'     => [
                "url(./fonts/roboto/Roboto-Regular-webfont.eot?xasd)",
                'yyy/zzz.css',
                "url(xxx/www/yyy/fonts/roboto/Roboto-Regular-webfont.eot?xasd)",
            ],
            //
            'relative-parent-simple-quotes'           => [
                "url('../fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
                'yyy/zzz.css',
                "url('xxx/www/yyy/roboto/Roboto-Regular-webfont.eot?xasd')",
            ],
            'relative-parent-double-quotes'           => [
                "url('../fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
                'yyy/zzz.css',
                "url('xxx/www/yyy/roboto/Roboto-Regular-webfont.eot?xasd')",
            ],
            'relative-parent-no-quotes'               => [
                "url('../fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
                'yyy/zzz.css',
                "url('xxx/www/yyy/roboto/Roboto-Regular-webfont.eot?xasd')",
            ],
            //
            'absolute-simple-quotes'                  => [
                "url('/fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
                'yyy/zzz.css',
                "url('xxx/www/fonts/roboto/Roboto-Regular-webfont.eot?xasd')",
            ],
            'absolute-double-quotes'                  => [
                'url("/fonts/roboto/Roboto-Regular-webfont.eot?xasd")',
                'yyy/zzz.css',
                'url("xxx/www/fonts/roboto/Roboto-Regular-webfont.eot?xasd")',
            ],
            'absolute-no-quotes'                      => [
                'url(/fonts/roboto/Roboto-Regular-webfont.eot?xasd)',
                'yyy/zzz.css',
                'url(xxx/www/fonts/roboto/Roboto-Regular-webfont.eot?xasd)',
            ],
            //
            'absolute-more'                           => [
                "url(/fonts/Roboto-Regular-webfont.eot?xasd), url('/fonts/Roboto-Bold-webfont.eot?dsa')",
                'yyy/zzz.css',
                "url(xxx/www/fonts/Roboto-Regular-webfont.eot?xasd), url('xxx/www/fonts/Roboto-Bold-webfont.eot?dsa')",
            ],
            //
            'absolute-mixed-case'                     => [
                "uRl(/fonts/Roboto-Regular-webfont.eot?xasd), urL('/fonts/Roboto-Bold-webfont.eot?dsa')",
                'yyy/zzz.css',
                "url(xxx/www/fonts/Roboto-Regular-webfont.eot?xasd), url('xxx/www/fonts/Roboto-Bold-webfont.eot?dsa')",
            ],
            //
            'relative-overdone'                       => [
                "url(../../../../../../fonts/Roboto-Regular-webfont.eot?xasd)",
                'yyy/zzz.css',
                "url(xxx/www/Roboto-Regular-webfont.eot?xasd)",
            ],
        ];
    }

    /**
     * @dataProvider fixCssProvider
     *
     * @param string $content
     * @param string $baseUrl
     * @param string $expectedOutput
     */
    public function testFixCss(string $content, string $baseUrl, string $expectedOutput)
    {
        $actualOutput = $this->sut->fixCss($content, $baseUrl);

        $this->assertSame($expectedOutput, $actualOutput);
    }
}
