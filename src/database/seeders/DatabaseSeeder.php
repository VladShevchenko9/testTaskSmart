<?php

namespace Database\Seeders;

use App\Enums\MediaCollection;
use App\Enums\Role as RoleEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $role) {
            Role::findOrCreate($role->value);
        }

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole(RoleEnum::ADMIN->value);

        $customer = User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
        ]);
        $customer->assignRole(RoleEnum::CUSTOMER->value);

        $tickets = Ticket::factory(2)->create([
            'customer_id' => $customer->id,
        ]);

        foreach ($tickets as $ticket) {
            $ticket
                ->addMediaFromString('fake content')
                ->usingFileName('test-' . fake()->uuid() . '.txt')
                ->toMediaCollection(MediaCollection::ATTACHMENTS->value);
        }
    }
}
