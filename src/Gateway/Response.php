<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response implements Arrayable, Responsable
{
    /**
     * The order instance.
     */
    protected Order $order;

    /**
     * The redirect URL.
     */
    protected string $url = '/';

    /**
     * The response data.
     */
    protected array $data = [];

    /**
     * Create a new response instance.
     */
    public function __construct(Order $order, string $url = '/', array $data = [])
    {
        $this->order = $order;
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
     * Merge the response data.
     */
    public function with(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return array_merge($this->data, [
            'url' => $this->url,
            'status' => $this->order->status,
        ]);
    }

    /**
     * Convert the response to an HTTP response.
     */
    public function toResponse($request): BaseResponse
    {
        return $request->expectsJson()
            ? new JsonResponse($this->toArray())
            : new RedirectResponse($this->url);
    }
}
