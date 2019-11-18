<?php

namespace Rjvandoesburg\NovaUrlRewrite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Nova\Resource;

/**
 * Class UrlRewrite
 *
 * @package Rjvandoesburg\NovaUrlRewrite\Models
 *
 * @property int $id
 * @property int $group
 * @property string $request_path
 * @property string $target_path
 * @property int $redirect_type
 * @property null|string $description
 * @property null|string $model_type
 * @property null|int $model_id
 * @property \Illuminate\Database\Eloquent\Model|null $model
 * @property \Laravel\Nova\Resource|null $resource
 * @property null|string $resource_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class UrlRewrite extends Model
{
    public const FORWARD = 0;
    public const PERMANENT = 1;
    public const TEMPORARY = 2;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('url_rewrite.tables.url_rewrites'));
    }

    /**
     * @param  string  $value
     */
    public function setRequestPathAttribute(string $value): void
    {
        Arr::set($this->attributes, 'request_path', '/'.ltrim($value, '/'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Laravel\Nova\Resource|null
     */
    public function getResourceAttribute(): ?\Laravel\Nova\Resource
    {
        if (empty($this->resource_type) || ! class_exists($this->resource_type)) {
            return null;
        }

        return new $this->resource_type($this->model);
    }

    /**
     * @return bool
     */
    public function isForward(): bool
    {
        return $this->redirect_type === static::FORWARD;
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->redirect_type !== static::FORWARD;
    }

    /**
     * @return int
     */
    public function getRedirectType(): int
    {
        return $this->redirect_type === static::PERMANENT ? 301 : 302;
    }

    /**
     * @return array
     */
    public static function getRedirectTypeOptionsArray(): array
    {
        return [
            static::FORWARD   => __('No redirect'),
            static::PERMANENT => __('Permanent (301)'),
            static::TEMPORARY => __('Temporary (302)'),
        ];
    }
}
