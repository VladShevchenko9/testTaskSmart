<?php

namespace App\Services;

use App\Enums\MediaCollection;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

readonly class TicketService
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private UserRepositoryInterface   $userRepository
    )
    {
    }

    public function getAdminTickets(array $filters): LengthAwarePaginator
    {
        $rawPerPage = (int)($filters['per_page'] ?? 10);
        $perPage = max(1, min(100, $rawPerPage));

        return $this->ticketRepository->paginateForAdmin($filters, $perPage);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, UploadedFile> $attachments
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function createTicket(array $data, array $attachments = []): Ticket
    {
        $customerEmail = $data['email'];
        $customerName = $data['name'];

        $customer = $this->userRepository->findOrCreateByEmail($customerEmail, $customerName);
        if ($customer->name !== $customerName) {
            $customer = $this->userRepository->update($customer, ['name' => $customerName]);
        }

        $ticket = $this->ticketRepository->create([
            'customer_id' => $customer->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => TicketStatus::NEW->value,
        ]);

        foreach ($attachments as $attachment) {
            if ($attachment instanceof UploadedFile) {
                $ticket->addMedia($attachment)->toMediaCollection(MediaCollection::ATTACHMENTS->value);
            }
        }

        return $this->ticketRepository->load($ticket, ['customer', 'media']);
    }

    public function updateTicketStatus(Ticket $ticket, string $status): Ticket
    {
        $updatedTicket = $this->ticketRepository->update($ticket, [
            'status' => $status,
            'manager_reply_at' => now(),
        ]);

        return $this->ticketRepository->load($updatedTicket, ['customer', 'media']);
    }

    public function getTicketsStatistics(): array
    {
        return $this->ticketRepository->getTicketsStatistics();
    }
}
