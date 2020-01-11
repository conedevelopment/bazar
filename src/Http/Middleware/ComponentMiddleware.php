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

        if ($request->isMethod('GET') && $request->header('X-Inertia-Version') !== Bazar::assetVersion()) {
            if ($request->hasSession()) {
                $request->session()->reflash();
            }

            return Response::make('', 409, ['X-Inertia-Location' => $request->fullUrl()]);
        }

        if ($response instanceof Redirect
            && $response->getStatusCode() === 302
            && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $response->setStatusCode(303);
        }

        return $response;
    }
}
