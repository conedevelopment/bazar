<?php

namespace Bazar\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Apply all the relevant filters on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        foreach ($request->except('filter') as $name => $value) {
            if ($this->hasNamedScope($name) && ! is_null($value)) {
                $query = $this->callNamedScope($name, [$query, $value]);
            }
        }

        return $query;
    }

    /**
     * Exclude the given models from the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|string|array  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExclude(Builder $query, $value): Builder
    {
        return $query->whereNotIn('id', (array) $value);
    }

    /**
     * Scope the query by the given state.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeState(Builder $query, string $value): Builder
    {
        if (! in_array(SoftDeletes::class, class_uses($this))) {
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
     * Sort the query by the given order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, array $value = []): Builder
    {
        return $query->orderBy(
            $value['by'] ?? 'created_at', $value['order'] ?? 'desc'
        );
    }
}
