<?php

namespace Bazar\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\Component;

class Form extends Component
{
    /**
     * The model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * The action of the form.
     *
     * @var string
     */
    public $action;

    /**
     * Create a new component instance.
     *
     * @param  string  $action
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function __construct(string $action, Model $model)
    {
        $this->model = $model;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return ViewFactory::make('bazar::components.form');
    }
}
