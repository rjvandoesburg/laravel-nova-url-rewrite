<?php

namespace Rjvandoesburg\NovaUrlRewrite;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Rjvandoesburg\NovaUrlRewrite\Nova\UrlRewrite;

class NovaUrlRewriteTool extends Tool
{
    /**
     * @var string
     */
    protected $urlRewriteResource = UrlRewrite::class;

    /**
     * Perform any tasks that need to happen on tool registration.
     *
     * @return void
     */
    public function boot(): void
    {
        UrlRewrite::$model = config('url_rewrite.models.url_rewrite');

        Nova::resources([
            $this->urlRewriteResource,
        ]);
    }

    /**
     * @param  string  $resource
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\NovaUrlRewriteTool
     */
    public function setUrlRewriteResource(string $resource): self
    {
        $this->urlRewriteResource = $resource;

        return $this;
    }
}
