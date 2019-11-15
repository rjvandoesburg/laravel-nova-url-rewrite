<?php

namespace Rjvandoesburg\NovaUrlRewrite\Contracts;

use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

interface UrlRewriteBuilder
{
    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function create(): UrlRewrite;

    /**
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $urlRewrite
     *
     * @return bool
     * @throws \Rjvandoesburg\NovaUrlRewrite\Exceptions\UrlRewriteBuilderException
     */
    public function regenerate(UrlRewrite $urlRewrite): bool;
}
