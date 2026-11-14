<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sale;
use App\Models\Batch;
use App\Models\User;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'station_id' => null,
            'transaction_number' => 'SALE-' . strtoupper($this->faker->unique()->bothify('########??')),
            'sale_date' => now()->toDateString(),
            'heads_sold' => $this->faker->numberBetween(10, 50),
            'total_weight_kg' => $this->faker->randomFloat(2, 10, 100),
            'price_per_kg' => $this->faker->randomFloat(2, 120, 150),
            'total_amount' => function (array $attributes) {
                return $attributes['heads_sold'] * $attributes['price_per_kg'];
            },
            'buyer_name' => $this->faker->name,
            'status' => 'completed',
        ];
    }
}
