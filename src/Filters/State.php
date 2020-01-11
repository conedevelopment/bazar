<?php

namespace Bazar\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class State extends Filter
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
        if (! in_array(SoftDeletes::class, class_uses($query->getModel()))) {
            return $query;
        }

        switch ($value) {
            case 'all':
                return $query->withTrashed();
            case 'trashed':
                return $query->onlyTrashed();
            default:
                return $query;
        }
    }

    /**
     * Get filter options.
     *
     * @return array
     */
    public function options(): array
    {
        return [
            'all' => __('All'),
            'available' => __('Available'),
            'trashed' => __('Trashed'),
        ];
    }
}
