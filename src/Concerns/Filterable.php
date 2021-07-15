<?php

namespace Cone\Bazar\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Get the filter options for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function filters(Request $request): array
    {
        return in_array(SoftDeletes::class, class_uses_recursive(get_called_class()))
            ? ['state' => [
                __('All') => 'all',
                __('Available') => 'available',
                __('Trashed') => 'trashed',
            ]]
            : [];
    }

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
                $this->callNamedScope($name, [$query, $value]);
            }
        }

        return $query;
    }

    /**
     * Exclude the given models from the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExclude(Builder $query, array $value): Builder
    {
        return $query->whereNotIn($query->qualifyColumn('id'), $value);
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
        [$order, $by] = [
            $value['order'] ?? 'desc',
            str_replace('.', '->', $value['by'] ?? 'created_at')
        ];

        $by = $query->getModel()
                    ->getConnection()
                    ->getSchemaBuilder()
                    ->hasColumn($query->getModel()->getTable(), explode('->', $by, 2)[0])
                    ? $query->qualifyColumn($by)
                    : $by;

        return $query->orderBy($by, $order);
    }
}
