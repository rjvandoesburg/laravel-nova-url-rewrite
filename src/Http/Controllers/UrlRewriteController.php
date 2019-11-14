<?php

namespace Rjvandoesburg\NovaUrlRewrite\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository as UrlRewriteRepositoryContract;

class UrlRewriteController
{
    /**
     * @var \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository
     */
    protected $repository;

    /**
     * RewriteController constructor.
     *
     * @param  \Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository  $repository
     */
    public function __construct(UrlRewriteRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  string  $requestPath
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(string $requestPath)
    {
        if (($urlRewrite = $this->repository->getByRequestPath($requestPath, 0)) === null) {
            abort(404);
        }

        if ($urlRewrite->isForward()) {
            return $this->forwardResponse($urlRewrite->target_path);
        }

        return redirect($urlRewrite->target_path, $urlRewrite->getRedirectType());
    }

    /**
     * @param  string  $url
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function forwardResponse(string $url): \Symfony\Component\HttpFoundation\Response
    {
        return Route::dispatch(Request::create($url, request()->getMethod()));
    }
}
