<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    private const PERIODS = ['P1W', 'P1M', 'P1Y'];

    public function definition(): array
    {
        $n = array_rand(self::PERIODS);
        return [
            'period' => self::PERIODS[$n],
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            $period = new \DateInterval($attributes['period']);
            $delta = ['P1W' => '-1 week', 'P1M' => '-1 month', 'P1Y' => '-1 year'][$attributes['period']];
            $start = fake()->dateTimeBetween($delta, 'now');

            return [
                'start_at' => $start,
                'end_at' => (clone $start)->add($period),
            ];
        });
    }

    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            $period = new \DateInterval($attributes['period']);
            $end = fake()->dateTime('yesterday');

            return [
                'start_at' => (clone $end)->sub($period),
                'end_at' => $end,
            ];
        });

    }

    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            $period = new \DateInterval($attributes['period']);
            $start = fake()->dateTime('tomorrow');

            return [
                'start_at' => $start,
                'end_at' => (clone $start)->add($period),
            ];
        });

    }
}
