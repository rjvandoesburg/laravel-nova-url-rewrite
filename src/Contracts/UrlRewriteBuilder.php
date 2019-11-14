<?php

namespace Rjvandoesburg\NovaUrlRewrite\Contracts;

use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

interface UrlRewriteBuilder
{
    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function create(): UrlRewrite;
}
