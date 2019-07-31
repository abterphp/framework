<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\Factory;

use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;

class Minifier
{
    /**
     * @return CssMinifier
     */
    public function createCssMinifier(): CssMinifier
    {
        return new CssMinifier();
    }

    /**
     * @return JsMinifier
     */
    public function createJsMinifier(): JsMinifier
    {
        return new JsMinifier();
    }
}
