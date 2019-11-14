<?php

namespace Rjvandoesburg\NovaUrlRewrite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder as UrlRewriteBuilderContract;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository as UrlRewriteRepositoryContract;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

class UrlRewriteRepository implements UrlRewriteRepositoryContract
{
    /**
     * @var \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    protected $model;

    /**
     * UrlRewriteRepository constructor.
     *
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $model
     */
    public function __construct(UrlRewrite $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function getModel(): UrlRewrite
    {
        return $this->model;
    }

    /**
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $model
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository
     */
    public function setModel(UrlRewrite $model): UrlRewriteRepositoryContract
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  int  $id
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite|null
     */
    public function find(int $id): ?UrlRewrite
    {
        return $this->getModelQuery()
            ->whereKey($id)
            ->first();
    }

    /**
     * @param  int  $id
     *
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->getModelQuery()
            ->whereKey($id)
            ->exists();
    }

    /**
     * @param  string  $requestPath
     * @param  int  $group
     *
     * @return bool
     */
    public function requestPathExists(string $requestPath, int $group = 0): bool
    {
        return $this->getModelQuery()
            ->where('request_path', $requestPath)
            ->where('group', $group)
            ->exists();
    }

    /**
     * @param  string  $requestPath
     * @param  int  $group
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite|null
     */
    public function getByRequestPath(string $requestPath, int $group = 0): ?UrlRewrite
    {
        return $this->getModelQuery()
            ->where('request_path', '/'.ltrim($requestPath, '/'))
            ->where('group', $group)
            ->first();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return $this->getModelQuery()->get();
    }

    /**
     * @param  int  $id
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        return $this->getModelQuery()->findOrFail($id)->delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function getModelQuery()
    {
        return $this->model->newQueryWithoutScopes();
    }

    /**
     * @return \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder
     */
    public function getRewriteBuilder(): UrlRewriteBuilderContract
    {
        return app(UrlRewriteBuilderContract::class);
    }

    /**
     * @param  \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder  $builder
     *
     * @return \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite
     */
    public function create(UrlRewriteBuilderContract $builder): UrlRewrite
    {
        return $builder->create();
    }
}
