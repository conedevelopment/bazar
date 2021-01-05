<?php

namespace Bazar\Http\Middleware;

use Bazar\Bazar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;

class ComponentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->header('X-Bazar')) {
            return $response;
        }

        if ($request->isMethod('GET')
            && $request->hasHeader('X-Bazar-Version') && $request->header('X-Bazar-Version') !== Bazar::assetVersion()) {
            if ($request->hasSession()) {
                $request->session()->reflash();
            }

            return Response::make('', Redirect::HTTP_CONFLICT, ['X-Bazar-Location' => $request->fullUrl()]);
        }

        if ($response instanceof Redirect
            && $response->getStatusCode() === Redirect::HTTP_FOUND
            && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $response->setStatusCode(Redirect::HTTP_SEE_OTHER);
        }

        return $response;
    }
}
