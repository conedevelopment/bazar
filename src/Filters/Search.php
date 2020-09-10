<?php

namespace Bazar\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Search extends Filter
{
    /**
     * The searchable columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Create a new search filter instance.
     *
     * @param  array  $columns
     * @return void
     */
    public function __construct(array $columns = [])
    {
        $this->columns = $columns;
    }

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
        $columns = $this->map();

        return $query->where(function ($query) use ($columns, $value) {
            foreach ($columns as $key => $group) {
                $first = $key === array_key_first($columns);

                if (! is_array($group)) {
                    $query->{$first ? 'where' : 'orWhere'}($group, 'like', "%{$value}%");
                    continue;
                }

                $query->{$first ? 'whereHas' : 'orWhereHas'}($key, function ($query) use ($group, $value) {
                    foreach ($group as $index => $column) {
                        $query->{$index === 0 ? 'where' : 'orWhere'}($column, 'like', "%{$value}%");
                    }
                });
            }
        });
    }

    /**
     * Add columns.
     *
     * @param  string|array  $columns
     * @return $this
     */
    public function add($columns): Search
    {
        $this->columns = array_unique(array_merge($this->columns, (array) $columns));

        return $this;
    }

    /**
     * Remove columns.
     *
     * @param  string|array  $columns
     * @return $this
     */
    public function remove($columns): Search
    {
        $this->columns = array_unique(array_diff($this->columns, (array) $columns));

        return $this;
    }

    /**
     * Clear the columns.
     *
     * @return $this
     */
    public function clear(): Search
    {
        $this->columns = [];

        return $this;
    }

    /**
     * Map the searchable columns.
     *
     * @return array
     */
    protected function map(): array
    {
        return array_reduce($this->columns, function ($columns, $column) {
            return array_merge_recursive($columns, [
                Str::before($column, '.') => Str::contains($column, '.') ? [Str::after($column, '.')] : $column,
            ]);
        }, []);
    }
}
