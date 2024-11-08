<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Server>
 */

class ServerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'country' => $this->faker->countryCode(),
            'location' => $this->faker->city(),
            'capacity' => random_int(1, 10),
            'active' => true,
            'created_at' => (new \DateTimeImmutable())->format(\DateTimeImmutable::ATOM),
            'updated_at' => (new \DateTimeImmutable())->format(\DateTimeImmutable::ATOM),
        ];
    }
}
