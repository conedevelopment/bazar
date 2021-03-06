<?php

namespace Bazar\Database\Factories;

use Bazar\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'driver' => 'cash',
            'amount' => mt_rand(10, 1000) / 10,
            'type' => Arr::random([Transaction::PAYMENT, Transaction::REFUND]),
        ];
    }
}
