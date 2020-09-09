<?php

namespace Bazar\Contracts;

use Illuminate\Http\Request;

interface Breadcrumbable
{
    /**
     * Get the breadcrumb label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getBreadcrumbLabel(Request $request): string;
}
