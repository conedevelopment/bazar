<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Models\Order;
use Cone\Root\Fields\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderStatus extends Select
{
    /**
     * Create a new field instance.
     */
    public function __construct(?string $label = null, Closure|string $modelAttribute = 'status')
    {
        parent::__construct($label ?: __('Status'), $modelAttribute);

        $this->options(Order::getStatuses());
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        $value = parent::resolveFormat($request, $model);

        if (is_null($value)) {
            return $value;
        }

        return sprintf(
            '<span class="status %s">%s</span>',
            $this->valueToClass($this->resolveValue($request, $model)),
            $value,
        );
    }

    /**
     * Convert the value to a class name.
     */
    public function valueToClass(string $value): string
    {
        return match ($value) {
            Order::PENDING, Order::ON_HOLD => 'status--warning',
            Order::CANCELLED, Order::FAILED => 'status--danger',
            Order::FULFILLED => 'status--success',
            Order::IN_PROGRESS => 'status--info',
            default => 'status--info',
        };
    }
}
