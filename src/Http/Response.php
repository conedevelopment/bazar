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
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): BaseResponse
    {
        $component = View::make($this->component, $this->props);

        $props = $component->getData();

        array_walk_recursive($props, function (&$prop) {
            if ($prop instanceof Closure) {
                $prop = App::call($prop);
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

        return $request->header('X-Inertia')
            ? ResponseFactory::json($page)->withHeaders([
                'Vary' => 'Accept',
                'X-Inertia' => 'true',
            ])
            : ResponseFactory::view('bazar::app', compact('page'));
    }
}
