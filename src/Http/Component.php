<?php

namespace Bazar\Http;

use Bazar\Bazar;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as Factory;

class Component implements Responsable
{
    /**
     * The view name.
     *
     * @var string
     */
    protected $view;

    /**
     * The view data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new response instance.
     *
     * @param  string  $view
     * @param  array  $data
     * @return void
     */
    public function __construct(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Push a property to the stack.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function with($key, $value = null): Component
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
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request): Response
    {
        // $request->header('X-Bazar-Only')

        if ($request->header('X-Bazar')) {
            return Factory::view($this->view, $this->data)->withHeaders([
                'Vary' => 'Accept',
                'X-Bazar' => true,
                'X-Bazar-Component' => str_replace(['::', '.'], '-', $this->view),
                'X-Bazar-Version' => Bazar::assetVersion(),
                'X-Bazar-Location' => $request->getRequestUri(),
            ]);
        }

        return Factory::view('bazar::app', array_merge($this->data, [
                '__view' => $this->view,
                '__page' => [
                    'version' => Bazar::assetVersion(),
                    'location' => $request->getRequestUri(),
                    'component' => str_replace(['::', '.'], '-', $this->view),
                ],
            ]
        ));
    }
}
