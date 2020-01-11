<?php

namespace Bazar\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    /**
     * Make a new filter instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters): Filter
    {
        return new static(...$parameters);
    }

    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply(Builder $query, Request $request, $value): Builder;

    /**
     * Get filter options.
     *
     * @return array
     */
    public function options(): array
    {
        return [];
    }

    /**
     * Get the key of the filter.
     *
     * @return string
     */
    public function key(): string
    {
        return strtolower(class_basename($this));
    }
}
