<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Inertia\Inertia;
use Inertia\Response;

class PagesController extends Controller
{
    /**
     * Show the dashboard page.
     *
     * @return \Inertia\Response
     */
    public function dashboard(): Response
    {
        return Inertia::render('Dashboard');
    }

    /**
     * Show the support page.
     *
     * @return \Inertia\Response
     */
    public function support(): Response
    {
        return Inertia::render('Support', [
            'version' => Bazar::getVersion(),
        ]);
    }
}
