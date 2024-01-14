<?php

namespace Cone\Bazar\Gateway;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response implements Arrayable, Responsable
{
    /**
     * The redirect URL.
     */
    protected string $url = '/';

    /**
     * The response data.
     */
    protected array $data = [];

    /**
     * The response resolver.
     */
    protected ?Closure $responseResolver = null;

    /**
     * Create a new response instance.
     */
    public function __construct(string $url = '/', array $data = [])
    {
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * Set the redirect URL.
     */
    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the response data.
     */
    public function data(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Create a custom response.
     */
    public function respondWith(Closure $callback): static
    {
        $this->responseResolver = $callback;

        return $this;
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return array_merge($this->data, [
            'url' => $this->url,
        ]);
    }

    /**
     * Convert the response to an HTTP response.
     */
    public function toResponse($request): BaseResponse
    {
        if (! is_null($this->responseResolver)) {
            return call_user_func_array($this->responseResolver, [$this->url, $this->data]);
        } elseif ($request->wantsJson()) {
            new JsonResponse($this->toArray());
        }

        return new RedirectResponse($this->url);
    }
}
