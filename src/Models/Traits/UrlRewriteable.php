<?php

namespace Rjvandoesburg\NovaUrlRewrite\Models\Traits;

use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

/**
 * Trait UrlRewriteable
 *
 * @package Rjvandoesburg\NovaUrlRewrite\Models\Traits
 *
 * @property-read \Illuminate\Support\Collection|\Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite[] $urlRewrites
 */
trait UrlRewriteable
{
    /**
     * @return mixed
     */
    public function urlRewrites()
    {
        return $this->morphMany(UrlRewrite::class, 'model');
    }
}
