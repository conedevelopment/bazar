<?php

namespace Bazar\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Filters
{
    /**
     * The registered filters.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Create a new filters instance.
     *
     * @param  string  $model
     * @param  array  $filters
     * @return void
     */
    public function __construct(string $model, array $filters = [])
    {
        $this->filters = array_merge(
            [Search::make(), Sort::make(), Exclude::make()],
            in_array(SoftDeletes::class, class_uses($model)) ? [State::make()] : [],
            $filters
        );
    }

    /**
     * Make a new filters instance.
     *
     * @param  string  $model
     * @param  array  $filters
     * @return static
     */
    public static function make(string $model, array $filters = []): Filters
    {
        return new static($model, $filters);
    }

    /**
     * Apply the filters on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, Request $request): Builder
    {
        foreach ($this->filters as $filter) {
            if ($value = $request->input($filter->key())) {
                $filter->apply($query, $request, $value);
            }
        }

        return $query;
    }

    /**
     * Add the given filter.
     *
     * @param  \Bazar\Filters\Filter  $filter
     * @return $this
     */
    public function add(Filter $filter): Filters
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Remove the given filter.
     *
     * @param  string  $key
     * @return $this
     */
    public function remove(string $key): Filters
    {
        $this->filters = array_filter($this->filters, function (Filter $filter) use ($key) {
            return $filter->key() !== $key;
        });

        return $this;
    }

    /**
     * Get the filter with the given key.
     *
     * @param  string  $key
     * @return \Bazar\Filters\Filter|null
     */
    public function get(string $key): ?Filter
    {
        return Arr::first($this->filters, function (Filter $filter) use ($key) {
            return $filter->key() === $key;
        });
    }

    /**
     * Add searchable columns.
     *
     * @param  string|array  $columns
     * @return $this
     */
    public function searchIn($columns): Filters
    {
        /** @var \Bazar\Filters\Search|null  $filter */
        $filter = $this->get('search');
        if ($filter) {
            $filter->add($columns);
        }

        return $this;
    }

    /**
     * Add filters to the stack.
     *
     * @param  string|array  $filters
     * @return $this
     */
    public function with($filters): Filters
    {
        foreach ((array) $filters as $filter) {
            $this->add($filter);
        }

        return $this;
    }

    /**
     * Remove the default filters.
     *
     * @return $this
     */
    public function withoutDefault(): Filters
    {
        $this->filters = array_filter($this->filters, function (Filter $filter) {
            return ! in_array($filter->key(), ['search', 'sort', 'exclude', 'state']);
        });

        return $this;
    }

    /**
     * Get all the selectable options.
     *
     * @return array
     */
    public function options(): array
    {
        return array_filter(array_reduce($this->filters, function (array $filters, Filter $filter) {
            return array_merge($filters, [$filter->key() => $filter->options()]);
        }, []));
    }
}
