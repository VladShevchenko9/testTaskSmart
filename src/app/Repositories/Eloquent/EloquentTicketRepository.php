<?php

namespace App\Repositories\Eloquent;

use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * @extends AbstractEloquentRepository<Ticket>
 */
class EloquentTicketRepository extends AbstractEloquentRepository implements TicketRepositoryInterface
{
    protected function modelClass(): string
    {
        return Ticket::class;
    }

    public function paginateForAdmin(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->with('customer')
            ->when($filters['id'] ?? null, fn($query, $ticketId) => $query->where('id', $ticketId))
            ->when(
                $filters['customer_name'] ?? null,
                fn($query, $customerName) => $query->whereHas(
                    'customer',
                    fn($customerQuery) => $customerQuery->where('name', 'like', '%' . $customerName . '%')
                )
            )
            ->when(
                $filters['customer_email'] ?? null,
                fn($query, $customerEmail) => $query->whereHas(
                    'customer',
                    fn($customerQuery) => $customerQuery->where('email', 'like', '%' . $customerEmail . '%')
                )
            )
            ->when($filters['subject'] ?? null, fn($query, $subject) => $query->where('subject', 'like', '%' . $subject . '%'))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->when($filters['created_at'] ?? null, fn($query, $createdAt) => $query->whereDate('created_at', $createdAt))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($filters);
    }

    public function getTicketsStatistics(): array
    {
        $now = Carbon::now();
        $dayStart = $now->copy()->subDay();
        $weekStart = $now->copy()->subWeek();
        $monthStart = $now->copy()->subMonth();

        $ticketsQuery = $this->model->newQuery();

        return [
            'day' => $ticketsQuery->createdFrom($dayStart)->count(),
            'week' => $this->model->newQuery()->createdFrom($weekStart)->count(),
            'month' => $this->model->newQuery()->createdFrom($monthStart)->count(),
        ];
    }
}
