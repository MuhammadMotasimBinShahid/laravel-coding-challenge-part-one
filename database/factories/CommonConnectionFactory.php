<?php

namespace Database\Factories;

use App\Models\CommonConnection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommonConnection>
 */
class CommonConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'common_user_id' => User::factory(),
        ];
    }
}
