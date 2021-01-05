<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Http\Component;
use Illuminate\Support\Facades\Response;

class PagesController extends Controller
{
    /**
     * Show the dashboard page.
     *
     * @return \Bazar\Http\Component
     */
    public function dashboard(): Component
    {
        return Response::component('bazar::dashboard');
    }

    /**
     * Show the support page.
     *
     * @return \Bazar\Http\Component
     */
    public function support(): Component
    {
        return Response::component('bazar::support', [
            'version' => Bazar::version(),
        ]);
    }
}
