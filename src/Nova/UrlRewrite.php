<?php

namespace Rjvandoesburg\NovaUrlRewrite\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Rjvandoesburg\NovaUrlRewrite\Nova\Filters\RedirectTypeFilter;
use Rjvandoesburg\NovaUrlRewrite\Rules\UniqueRequestPath;

class UrlRewrite extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model;

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function fields(Request $request): array
    {
        // TODO: Add search field for resources (think landing page url redirects to product)

        $table = $this->model()->getTable();

        return [
            ID::make()->sortable(),

            Text::make(__('Request path'), 'request_path')
                ->onlyOnForms()
                ->rules('required')
                ->creationRules('required', new UniqueRequestPath($table, false))
                ->updateRules('required', new UniqueRequestPath($table, true)),

            Text::make(__('Target path'), 'target_path')
                ->onlyOnForms()
                ->rules('required'),

            Text::make(__('Request path'), 'request_path', function ($value) {
                $value = url($value);

                return "<a class='no-underline font-bold dim text-primary' href='{$value}' target='_blank'>{$value}</a>";
            })
                ->sortable()
                ->exceptOnForms()
                ->asHtml(),

            Text::make(__('Target path'), 'target_path', function ($value) {
                $value = url($value);

                return "<a class='no-underline font-bold dim text-primary' href='{$value}' target='_blank'>{$value}</a>";
            })
                ->sortable()
                ->exceptOnForms()
                ->asHtml(),

            Select::make(__('Redirect type'), 'redirect_type')->options(
                \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite::getRedirectTypeOptionsArray()
            )
                ->displayUsingLabels()
                ->sortable()
                ->rules('required'),

            Text::make(__('Description'), 'description')
                ->hideFromIndex(),

            DateTime::make(__('Created at'), 'created_at')
                ->onlyOnDetail(),

            DateTime::make(__('Updated at'), 'updated_at')
                ->onlyOnDetail(),
        ];
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [
            new RedirectTypeFilter,
        ];
    }
}
