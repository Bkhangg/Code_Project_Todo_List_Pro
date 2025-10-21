<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::first();

        return [
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([
                Task::NOT_STARTED,
                Task::IN_PROGRESS,
                Task::COMPLETED,
            ]),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'created_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'user_id' => $user->id
        ];
    }
}
