<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Http\Response;
use Bazar\Support\Facades\Component;

class PagesController extends Controller
{
    /**
     * Show the dashboard page.
     *
     * @return \Bazar\Http\Response
     */
    public function dashboard(): Response
    {
        return Component::render('bazar::admin.dashboard');
    }

    /**
     * Show the support page.
     *
     * @return \Bazar\Http\Response
     */
    public function support(): Response
    {
        return Component::render('bazar::admin.support', [
            'version' => Bazar::version(),
        ]);
    }
}
