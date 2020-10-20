<?php

namespace Bazar\View\Components;

use Bazar\Contracts\Breadcrumbable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Breadcrumbs extends Component
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The label replacers.
     *
     * @var array
     */
    protected $replacers = [
        'bazar' => 'dashboard',
    ];

    /**
     * Initialize a new component instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the breadcrumb items.
     *
     * @return array
     */
    protected function items(): array
    {
        $segments = $this->request->segments();

        $uris = explode('/', $this->request->route()->uri());

        return array_reduce($uris, function ($breadcrumbs, $uri) use ($segments) {
            $keys = array_keys($breadcrumbs);

            $segment = $segments[count($breadcrumbs)];

            $path = end($keys).'/'.$segment;

            return array_merge($breadcrumbs, [$path => $this->label($uri)]);
        }, []);
    }

    /**
     * Get the label for the URI.
     *
     * @param  string  $uri
     * @return string
     */
    protected function label(string $uri): string
    {
        if (preg_match('/(?<=\{).*(?=\})/', $uri, $match)) {
            $item = $this->request->route($match[0]);

            if ($item instanceof Breadcrumbable) {
                return $item->getBreadcrumbLabel($this->request);
            }
        }

        return Str::title($this->replacers[$uri] ?? $uri);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return ViewFactory::make('bazar::layout.breadcrumbs', [
            'items' => $this->items(),
        ]);
    }
}
