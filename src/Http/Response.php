<?php

namespace Bazar\Http;

use Bazar\Bazar;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response implements Responsable
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected $component;

    /**
     * The component props.
     *
     * @var array
     */
    protected $props;

    /**
     * The view data.
     *
     * @var array
     */
    protected $viewData = [];

    /**
     * Create a new response instance.
     *
     * @param  string  $component
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $props
     * @return void
     */
    public function __construct(string $component, $props)
    {
        $this->component = $component;
        $this->props = $props instanceof Arrayable ? $props->toArray() : $props;
    }

    /**
     * Push a property to the stack.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function with($key, $value = null): Response
    {
        if (is_array($key)) {
            $this->props = array_merge($this->props, $key);
        } else {
            $this->props[$key] = $value;
        }

        return $this;
    }

    /**
     * Push a property to the view data.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function withViewData($key, $value = null): Response
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): BaseResponse
    {
        $only = array_filter(explode(',', $request->header('X-Inertia-Partial-Data')));

        $props = ($only && $request->header('X-Inertia-Partial-Component') === $this->component)
            ? Arr::only($this->props, $only)
            : $this->props;

        array_walk_recursive($props, function (&$prop) use ($request) {
            if ($prop instanceof Closure) {
                $prop = App::call($prop);
            }

            if ($prop instanceof Responsable) {
                $prop = $prop->toResponse($request)->getData();
            }

            if ($prop instanceof Arrayable) {
                $prop = $prop->toArray();
            }
        });

        $page = [
            'component' => $this->component,
            'props' => $props,
            'url' => $request->getRequestUri(),
            'version' => Bazar::assetVersion(),
        ];

        if ($request->header('X-Inertia')) {
            return ResponseFactory::json($page, 200, [
                'Vary' => 'Accept',
                'X-Inertia' => 'true',
            ]);
        }

        return ResponseFactory::view('bazar::app', $this->viewData + ['page' => $page]);
    }
}
