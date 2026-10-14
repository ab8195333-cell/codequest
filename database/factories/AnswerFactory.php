<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Question;
/**
 * @extends Factory<Model>
 */
class AnswerFactory extends Factory
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
            'question_id' => Question::factory(),
            'answer_text' => fake()->paragraph(),
        ];
    }
}
