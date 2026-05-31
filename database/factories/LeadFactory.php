<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'source' => LeadSource::QuizContact->value,
            'quiz_answers' => [
                'device' => 'laptop',
                'device_label' => 'Ноутбук',
                'problems' => ['Не включается'],
                'brand' => 'Apple',
            ],
            'name' => fake()->name(),
            'phone' => '+375 (29) 123-45-67',
            'comment' => fake()->optional()->sentence(),
            'ip' => '127.0.0.1',
        ];
    }
}
