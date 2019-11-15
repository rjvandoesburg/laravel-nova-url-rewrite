<?php

namespace Rjvandoesburg\NovaUrlRewrite\Contracts;

use Illuminate\Support\Collection;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder as UrlRewriteBuilderContract;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

interface UrlRewriteRepository
{
    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function getModel(): UrlRewrite;

    /**
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $model
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository
     */
    public function setModel(UrlRewrite $model): self;

    /**
     * @param  int  $id
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite|null
     */
    public function find(int $id): ?UrlRewrite;

    /**
     * @param  int  $id
     *
     * @return bool
     */
    public function exists(int $id): bool;

    /**
     * @param  string  $requestPath
     * @param  int  $group
     *
     * @return bool
     */
    public function requestPathExists(string $requestPath, int $group = 0): bool;

    /**
     * @param  string  $requestPath
     * @param  int  $group
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite|null
     */
    public function getByRequestPath(string $requestPath, int $group = 0): ?UrlRewrite;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection;

    /**
     * @param  int  $id
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function getModelQuery();

    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder
     */
    public function getRewriteBuilder(): UrlRewriteBuilderContract;

    /**
     * @param  \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder  $builder
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function create(UrlRewriteBuilderContract $builder): UrlRewrite;
}
