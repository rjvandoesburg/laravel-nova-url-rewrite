<?php

namespace Rjvandoesburg\NovaUrlRewrite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder as UrlRewriteBuilderContract;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

class UrlRewriteBuilder implements UrlRewriteBuilderContract
{

    /**
     * @var \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    protected $urlRewrite;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \Laravel\Nova\Resource
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $unique = false;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * RewriteBuilder constructor.
     *
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $urlRewrite
     */
    public function __construct(UrlRewrite $urlRewrite)
    {
        $this->urlRewrite = $urlRewrite;
    }

    /**
     * @param  int  $group
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setGroup(int $group): self
    {
        Arr::set($this->attributes, 'group', $group);

        return $this;
    }

    /**
     * @param  string  $requestPath
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setRequestPath(string $requestPath): self
    {
        Arr::set($this->attributes, 'request_path', $requestPath);

        return $this;
    }

    /**
     * @param  string  $targetPath
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setTargetPath(string $targetPath): self
    {
        Arr::set($this->attributes, 'target_path', $targetPath);

        return $this;
    }

    /**
     * @param  int  $type
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setRedirectType(int $type): self
    {
        Arr::set($this->attributes, 'type', $type);

        return $this;
    }

    /**
     * @param  string  $description
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setDescription(string $description): self
    {
        Arr::set($this->attributes, 'description', $description);

        return $this;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  \Laravel\Nova\Resource  $resource
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setResource(\Laravel\Nova\Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @param  bool  $unique
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function setUnique(bool $unique = true): self
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function create(): UrlRewrite
    {
        return $this->urlRewrite->newQueryWithoutScopes()->create($this->attributes);
    }
}
