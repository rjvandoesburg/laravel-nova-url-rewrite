<?php

namespace Rjvandoesburg\NovaUrlRewrite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder as UrlRewriteBuilderContract;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository as UrlRewriteRepositoryContract;
use Rjvandoesburg\NovaUrlRewrite\Exceptions\InvalidRedirectTypeExeption;
use Rjvandoesburg\NovaUrlRewrite\Exceptions\RequestPathExistsException;
use Rjvandoesburg\NovaUrlRewrite\Exceptions\UrlRewriteBuilderException;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

class UrlRewriteBuilder implements UrlRewriteBuilderContract
{
    /**
     * @var \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository
     */
    protected $repository;

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
     * @param  \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository  $repository
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $urlRewrite
     */
    public function __construct(UrlRewriteRepositoryContract $repository, UrlRewrite $urlRewrite)
    {
        $this->urlRewrite = $urlRewrite;
        $this->repository = $repository;
    }

    /**
     * @param  int  $group
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function group(int $group): self
    {
        Arr::set($this->attributes, 'group', $group);

        return $this;
    }

    /**
     * @param  string  $requestPath
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function requestPath(string $requestPath): self
    {
        Arr::set($this->attributes, 'request_path', '/'.ltrim($requestPath, '/'));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequestPath(): ?string
    {
        return Arr::get($this->attributes, 'request_path');
    }

    /**
     * @param  string  $targetPath
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function targetPath(string $targetPath): self
    {
        Arr::set($this->attributes, 'target_path', $targetPath);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTargetPath(): ?string
    {
        if (! empty($targetPath = Arr::get($this->attributes, 'target_path'))) {
            return $targetPath;
        }

        if ($this->model === null) {
            return null;
        }

        if ($this->resource === null) {
            // Now technically because it is based on Nova resources I'm not quite sure if this is wanted...
            $resourceKey = Str::plural(Str::kebab(class_basename($this->model)));

            return "/{$resourceKey}/{$this->model->getRouteKey()}";
        }

        return "/{$this->resource::uriKey()}/{$this->model->getRouteKey()}";
    }

    /**
     * @param  int  $type
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     * @throws \Rjvandoesburg\NovaUrlRewrite\Exceptions\InvalidRedirectTypeExeption
     */
    public function redirectType(int $type): self
    {
        if (! \array_key_exists($type, UrlRewrite::getRedirectTypeOptionsArray())) {
            throw new InvalidRedirectTypeExeption(__('Redirect type ":type" is invalid.', [
                'type' => $type,
            ]));
        }

        Arr::set($this->attributes, 'redirect_type', $type);

        return $this;
    }

    /**
     * @param  string  $description
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function description(string $description): self
    {
        Arr::set($this->attributes, 'description', $description);

        return $this;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function model(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  \Laravel\Nova\Resource  $resource
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function resource(\Laravel\Nova\Resource $resource): self
    {
        $this->resource = $resource;

        if ($this->model === null && optional($resource->model())->exists) {
            $this->model($this->resource->model());
        }

        return $this;
    }

    /**
     * @param  bool  $unique
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder
     */
    public function unique(bool $unique = true): self
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @param  string  $requestPath
     * @param  int  $id
     *
     * @return string
     */
    protected function generateUnique(string $requestPath, int $id = 1): string
    {
        $path = "{$requestPath}-{$id}";

        if ($this->repository->requestPathExists($path)) {
            return $this->generateUnique($requestPath, $id + 1);
        }

        return $path;
    }

    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     * @throws \Rjvandoesburg\NovaUrlRewrite\Exceptions\RequestPathExistsException
     * @throws \Rjvandoesburg\NovaUrlRewrite\Exceptions\UrlRewriteBuilderException
     * @throws \Throwable
     */
    public function create(): UrlRewrite
    {
        if (empty($requestPath = $this->getRequestPath())) {
            throw new UrlRewriteBuilderException(__('Required parameter :parameter not set', [
                'parameter' => __('Request path'),
            ]));
        }

        if ($this->repository->requestPathExists($requestPath)) {
            if (! $this->unique) {
                throw new RequestPathExistsException(__('Request path ":path" already exists.', [
                    'path' => $requestPath,
                ]));
            }

            $requestPath = $this->generateUnique($requestPath);
        }

        if (empty($targetPath = $this->getTargetPath())) {
            throw new UrlRewriteBuilderException(__('Error while generating the target path'));
        }
        $attributes = $this->attributes;

        $attributes['request_path'] = $requestPath;
        $attributes['target_path'] = $targetPath;
        if ($this->resource !== null) {
            $attributes['resource_type'] = \get_class($this->resource);
        }

        return \DB::transaction(function () use ($attributes) {
            $urlRewrite = $this->urlRewrite->newQueryWithoutScopes()->create($attributes);

            $model = $this->model ?? optional($this->resource)->resource;

            if ($model !== null) {
                $urlRewrite->model()->associate($model);
                $urlRewrite->save();
            }

            return $urlRewrite;
        });
    }
}
