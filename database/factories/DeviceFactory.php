<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Polyfill\Uuid\Uuid;

/**
 * @extends Factory<\App\Models\Device>
 */

class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'model' => fake()->company(),
        ];
    }
}
