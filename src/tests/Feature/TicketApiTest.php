<?php

namespace Tests\Feature;

use App\Enums\Role as RoleEnum;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_creates_ticket_with_attachment(): void
    {
        Storage::fake('public');
        Role::findOrCreate(RoleEnum::CUSTOMER->value);

        $response = $this->post('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Need help',
            'message' => 'Please help me with my issue',
            'attachments' => [
                UploadedFile::fake()->create('evidence.txt', 100, 'text/plain'),
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Ticket created successfully')
            ->assertJsonPath('data.customer.email', 'john@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Need help',
            'status' => TicketStatus::NEW->value,
        ]);

        $ticket = Ticket::query()->firstOrFail();
        $this->assertCount(1, $ticket->getMedia('attachments'));
    }

    public function test_admin_can_list_show_and_update_tickets(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomerUser();

        $ticket = Ticket::query()->create([
            'customer_id' => $customer->id,
            'subject' => 'Initial subject',
            'message' => 'Initial message',
            'status' => TicketStatus::NEW->value,
        ]);

        $listResponse = $this->actingAs($admin)->get('/admin/api/tickets');
        $listResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticket->id);

        $showResponse = $this->actingAs($admin)->get("/admin/api/tickets/{$ticket->id}");
        $showResponse
            ->assertOk()
            ->assertJsonPath('data.id', $ticket->id)
            ->assertJsonPath('data.subject', 'Initial subject');

        $updateResponse = $this->actingAs($admin)->patch("/admin/api/tickets/{$ticket->id}", [
            'status' => TicketStatus::IN_PROGRESS->value,
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('data.status', TicketStatus::IN_PROGRESS->value);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::IN_PROGRESS->value,
        ]);
    }

    public function test_admin_statistics_returns_day_week_month_counts(): void
    {
        Role::findOrCreate(RoleEnum::CUSTOMER->value);
        $admin = $this->createAdminUser();
        $customer = $this->createCustomerUser();

        Carbon::setTestNow(Carbon::parse('2026-06-02 12:00:00'));

        $todayTicket = Ticket::query()->create([
            'customer_id' => $customer->id,
            'subject' => 'today',
            'message' => 'today',
            'status' => TicketStatus::NEW->value,
        ]);
        $todayTicket->forceFill([
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(5),
        ])->save();

        $weekTicket = Ticket::query()->create([
            'customer_id' => $customer->id,
            'subject' => 'this week',
            'message' => 'this week',
            'status' => TicketStatus::NEW->value,
        ]);
        $weekTicket->forceFill([
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3),
        ])->save();

        $monthTicket = Ticket::query()->create([
            'customer_id' => $customer->id,
            'subject' => 'this month',
            'message' => 'this month',
            'status' => TicketStatus::NEW->value,
        ]);
        $monthTicket->forceFill([
            'created_at' => Carbon::now()->subDays(20),
            'updated_at' => Carbon::now()->subDays(20),
        ])->save();

        $oldTicket = Ticket::query()->create([
            'customer_id' => $customer->id,
            'subject' => 'old ticket',
            'message' => 'old ticket',
            'status' => TicketStatus::NEW->value,
        ]);
        $oldTicket->forceFill([
            'created_at' => Carbon::now()->subDays(45),
            'updated_at' => Carbon::now()->subDays(45),
        ])->save();

        $response = $this->actingAs($admin)->get('/admin/api/tickets/statistics');

        $response
            ->assertOk()
            ->assertJsonPath('data.day', 1)
            ->assertJsonPath('data.week', 2)
            ->assertJsonPath('data.month', 3);

        Carbon::setTestNow();
    }

    public function test_guest_cannot_access_admin_ticket_api_routes(): void
    {
        $this->get('/admin/api/tickets')->assertRedirect(route('login'));
        $this->get('/admin/api/tickets/statistics')->assertRedirect(route('login'));
    }

    private function createAdminUser(): User
    {
        Role::findOrCreate(RoleEnum::ADMIN->value);
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        return $admin;
    }

    private function createCustomerUser(): User
    {
        Role::findOrCreate(RoleEnum::CUSTOMER->value);
        $customer = User::factory()->create();
        $customer->assignRole(RoleEnum::CUSTOMER->value);

        return $customer;
    }
}
