<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;
/**
 * @extends Factory<Model>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'user_id' => User::factory(), // ينشئ مستخدم مرتبط بالبوست أو يربطه تلقائياً
            'category_id' => Category::factory(),
            'title' => fake()->sentence() . '؟',
            'description' => fake()->paragraphs(3, true),
            'views_count' => fake()->numberBetween(0, 500),
        ];
    }
}
