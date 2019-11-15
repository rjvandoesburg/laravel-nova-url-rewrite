<?php

namespace Rjvandoesburg\NovaUrlRewrite\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;

class Regenerate extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $failedCount = 0;
        foreach ($models as $model) {
            if (! $model instanceof UrlRewrite) {
                continue;
            }
            /** @var \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder $urlRewriteBuilder */
            $urlRewriteBuilder = app(UrlRewriteBuilder::class);

            try {
                $urlRewriteBuilder->regenerate($model);
            } catch (\Exception $exception) {
                $failedCount++;
                \Log::error($exception->getMessage(), [
                    'model' => $model,
                ]);
            }
        }

        $modelCount = $models->count();
        if ($failedCount > 0) {
            return Action::danger(__(':count rewrites regenerated, :failed failed. See logs for more details', [
                'count'  => $modelCount - $failedCount,
                'failed' => $failedCount,
            ]));
        }

        return Action::message(__(':count rewrites regenerated', [
            'count' => $modelCount,
        ]));
    }
}
