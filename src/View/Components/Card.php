<?php

namespace Bazar\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\Component;

class Card extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return ViewFactory::make('bazar::components.card', [
            //
        ]);
    }
}
