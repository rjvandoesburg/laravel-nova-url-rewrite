<?php

namespace Rjvandoesburg\NovaUrlRewrite\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

class RedirectTypeFilter extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value): Builder
    {
        return $query->where('redirect_type', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request): array
    {
        return \array_flip(UrlRewrite::getRedirectTypeOptionsArray());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return __('URL rewrite redirect type');
    }
}
