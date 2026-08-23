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
            'quantity' => $this->faker->numberBetween(10, 50),
            'price_per_kg' => $this->faker->randomFloat(2, 120, 150),
            'total_amount' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['price_per_kg'];
            },
            'customer_name' => $this->faker->name,
            'notes' => $this->faker->sentence,
        ];
    }
}