<?php

namespace Bazar\Http\Middleware;

use Bazar\Bazar;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;

class ComponentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        $response = $next($request);

        if (! $request->header('X-Inertia')) {
            return $response;
        }

        if ($response instanceof Redirect
            && $response->getStatusCode() === Redirect::HTTP_FOUND
            && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $response->setStatusCode(Redirect::HTTP_SEE_OTHER);
        }

        return $response;
    }
}
