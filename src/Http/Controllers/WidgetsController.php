<?php

namespace Bazar\Http\Controllers;

use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class WidgetsController extends Controller
{
    /**
     * Show the activities.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activities(): JsonResponse
    {
        $orders = Cache::remember('bazar.activities', 3600, static function (): Collection {
            return Order::proxy()->newQuery()->latest()->take(3)->get()->map(static function (Order $order): array {
                return [
                    'icon' => 'order',
                    'url' => route('bazar.orders.show', $order),
                    'title' => __('Order #:id', ['id' => $order->id]),
                    'description' => __('A new order was placed'),
                    'created_at' => $order->created_at->toAtomString(),
                    'formatted_created_at' => $order->created_at->diffForHumans(),
                ];
            });
        });

        return Response::json($orders);
    }

    /**
     * Show the metrics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function metrics(): JsonResponse
    {
        $metrics = Cache::remember('bazar.metrics', 3600, static function (): array {
            return [
                'users' => User::proxy()->newQuery()->count(),
                'orders' => Order::proxy()->newQuery()->count(),
                'products' => Product::proxy()->newQuery()->count(),
            ];
        });

        return Response::json($metrics);
    }

    /**
     * Show the sales.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sales(): JsonResponse
    {
        $sales = Cache::remember('bazar.sales', 3600, static function (): array {
            $days = array_reverse(array_reduce(array_fill(0, 7, null), static function (array $days): array {
                return array_merge($days, [Carbon::now()->subDays(count($days))->format('m-d')]);
            }, []));

            $orders = Order::proxy()->newQuery()->whereNotIn(
                'status', ['cancelled', 'failed']
            )->where(
                'created_at', '>=', Carbon::now()->subDays(7)->startOfDay()
            )->get()->groupBy(static function (Order $order): string {
                return $order->created_at->format('m-d');
            })->map(static function (Collection $group): int {
                return $group->count();
            })->toArray();

            return [
                'labels' => $days,
                'data' => array_values(array_replace(array_fill_keys($days, 0), $orders)),
            ];
        });

        return Response::json($sales);
    }
}
