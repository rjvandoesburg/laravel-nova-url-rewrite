<?php

namespace Rjvandoesburg\NovaUrlRewrite\Console;

use Illuminate\Console\Command;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository;
use Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite;
use Symfony\Component\Console\Helper\ProgressBar;

class RegenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova-url-rewrite:regenerate {id? : Id of the url to regenerate}
    {--g|group=0 : The group to rewrite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate url rewrites';

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar;

    /**
     * @var \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $failed = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->repository = app(UrlRewriteRepository::class);

        if (! empty($id = $this->argument('id'))) {
            $urlRewrite = $this->repository->find((int) $id);
            if ($urlRewrite === null) {
                $this->error("No rewrite found for id [{$id}]");
                return;
            }
            $this->regenerate($urlRewrite);

            return;
        }

        $group = $this->option('group');

        $query = $this->repository->getModelQuery()
            ->where('group', $group);


        $count = $query->count();
        $redrawFrequency = $count < 10 ? 1 : $count / 10;

        $this->createProgressBar($count, round($redrawFrequency));

        foreach ($query->cursor() as $urlRewrite) {
            $this->regenerate($urlRewrite);
            $this->progressBar->advance();
        }

        $this->finishProgressBar();
        $this->line('');

        if (($failedCount = count($this->failed)) > 0) {
            $this->error("{$failedCount} failed to regenerate");
            $this->table([
                'id',
                'group',
                'request_path',
                'target_path',
                'description',
                'model',
                'resource'
            ], collect($this->failed)->map(static function (UrlRewrite $urlRewrite) {
                $model = null;
                if (! empty($urlRewrite->model_type) && ! empty($urlRewrite->model_id)) {
                    $model = "{$urlRewrite->model_type} : {$urlRewrite->model_id}";
                }

                return [
                    $urlRewrite->id,
                    $urlRewrite->group,
                    $urlRewrite->request_path,
                    $urlRewrite->target_path,
                    $urlRewrite->description,
                    $model,
                    $urlRewrite->resource_type,
                ];
            }));
        }
    }

    /**
     * @param  \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite  $urlRewrite
     */
    protected function regenerate(UrlRewrite $urlRewrite): void
    {
        /** @var \Rjvandoesburg\NovaUrlRewrite\UrlRewriteBuilder $builder */
        $builder = $this->repository->getRewriteBuilder();

        try {
            $builder->regenerate($urlRewrite);
        } catch (\Exception $exception) {
            $this->failed[] = $urlRewrite;
        }
    }
    /**
     * @param int $max
     * @param int $redrawFrequency
     */
    public function createProgressBar($max = 0, $redrawFrequency = 1): void
    {
        $this->progressBar = new ProgressBar($this->getOutput(), $max);
        $this->progressBar->setProgress(0);
        $this->progressBar->setRedrawFrequency($redrawFrequency);
        $this->progressBar->setFormat('very_verbose');
    }

    /**
     * Finish the progress bar
     */
    public function finishProgressBar(): void
    {
        $this->progressBar->finish();
        $this->getOutput()->writeln('');
    }
}
