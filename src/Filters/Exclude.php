<?php

namespace Bazar\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Exclude extends Filter
{
    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, Request $request, $value): Builder
    {
        return $query->whereNotIn('id', (array) $value);
    }
}
