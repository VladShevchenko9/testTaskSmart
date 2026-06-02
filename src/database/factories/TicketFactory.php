<?php

namespace Database\Factories;

use App\Enums\Role as RoleEnum;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::role(RoleEnum::CUSTOMER->value)->inRandomOrder()->first()?->id,
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'manager_reply_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
