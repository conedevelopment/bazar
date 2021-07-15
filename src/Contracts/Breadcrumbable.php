<?php

namespace Cone\Bazar\Contracts;

use Illuminate\Http\Request;

interface Breadcrumbable
{
    /**
     * Get the breadcrumb representation of the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toBreadcrumb(Request $request): string;
}
