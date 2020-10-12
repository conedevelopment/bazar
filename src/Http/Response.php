<?php

namespace Bazar\Http;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Facades\View;
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
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): BaseResponse
    {
        $component = View::make($this->component, $this->props);

        $props = $component->getData();

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
            'props' => $props,
            'url' => $request->getRequestUri(),
            'component' => $component->render(),
        ];

        return $request->header('X-Inertia') ? ResponseFactory::json($page, BaseResponse::HTTP_OK, [
            'Vary' => 'Accept',
            'X-Inertia' => 'true',
        ]) : ResponseFactory::view('bazar::app', compact('page'));
    }
}
