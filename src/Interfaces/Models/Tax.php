<?php

namespace Cone\Bazar\Interfaces\Models;

interface Tax
{
    /**
     * Get the formatted tax.
     */
    public function format(): string;
}
