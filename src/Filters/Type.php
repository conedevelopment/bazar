<?php

namespace Bazar\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Type extends Filter
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
        switch ($value) {
            case 'image':
                return $query->where('mime_type', 'like', 'image%');
            case 'file':
                return $query->where('mime_type', 'not like', 'image%');
            default:
                return $query;
        }
    }
}
