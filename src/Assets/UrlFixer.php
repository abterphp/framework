<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

class UrlFixer
{
    /** @var string[] */
    protected $quotes = ['"', "'"];

    /** @var string */
    protected $cacheUrl = '';

    /**
     * UrlFixer constructor.
     *
     * @param string $cacheUrl
     */
    public function __construct(string $cacheUrl)
    {
        $this->cacheUrl = $cacheUrl;
    }

    /**
     * FixCSS tries to compensate for minification of CSS stylesheets
     * - It looks for URLs
     * - Maintains optional quotes used
     * - Calls fixUrl to modify the URL if needed
     *
     * @param string $content
     * @param string $path
     *
     * @return string
     */
    public function fixCss(string $content, string $path): string
    {
        if (!preg_match_all('/url\s*\(\s*(\S*)\s*\)/Umsi', $content, $matches)) {
            return $content;
        }

        foreach ($matches[1] as $i => $match) {
            if (mb_strlen($match) < 2) {
                continue;
            }

            $q = mb_substr($match, 0, 1);
            $q = in_array($q, $this->quotes, true) ? $q : '';

            if ($q && mb_substr($match, -1) !== $q) {
                continue;
            }

            $url = $q ? mb_substr($match, 1, -1) : $match;

            $fixedUrl = $this->fixUrl($url, $path);

            if ($fixedUrl == $url) {
                continue;
            }

            $search  = $matches[0][$i];
            $replace = 'url(' . $q . $fixedUrl . $q . ')';

            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    /**
     * Fix URL will try to turn a URL into a full qualified URI
     *
     * - Data URLs skipped
     * - Already fully qualitied URIs skipped
     *
     * @param string $url
     * @param string $path
     *
     * @return string
     */
    protected function fixUrl(string $url, string $path): string
    {
        if (mb_substr($url, 0, 5) === 'data:') {
            return $url;
        }

        if (strpos($url, '://') !== false) {
            return $url;
        }

        $ch = mb_substr($url, 0, 1);
        if ($ch === '/') {
            return $this->cacheUrl . $url;
        }

        while (mb_substr($url, 0, 2) == './') {
            $url = mb_substr($url, 2);
        }

        $out = 0;
        while (mb_substr($url, 0, 3) == '../') {
            $out++;
            $url = mb_substr($url, 3);
        }

        $pathDir = dirname($path);

        $urlParts  = explode(DIRECTORY_SEPARATOR, $url);
        $pathParts = $pathDir === '.' ? [] : explode(DIRECTORY_SEPARATOR, $pathDir);
        if (count($pathParts) < $out) {
            return $this->cacheUrl . DIRECTORY_SEPARATOR . $urlParts[count($urlParts) - 1];
        }

        $urlParts  = array_slice($urlParts, $out);
        $pathParts = array_slice($pathParts, $out - 1);

        $path = implode(DIRECTORY_SEPARATOR, $pathParts);
        $url  = implode(DIRECTORY_SEPARATOR, $urlParts);

        return $this->cacheUrl . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $url;
    }
}
