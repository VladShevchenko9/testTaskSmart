<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminTicketIndexRequest;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateStatusRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService             $ticketService,
        private readonly TicketRepositoryInterface $ticketRepository
    )
    {
    }

    public function welcome(): View
    {
        return view('welcome');
    }

    public function widget(): View
    {
        return view('tickets.widget');
    }

    public function adminTicketsPage(): View
    {
        return view('admin.tickets.index');
    }

    public function index(AdminTicketIndexRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $tickets = $this->ticketService->getAdminTickets($validated);

        return TicketResource::collection($tickets);
    }

    public function show(Ticket $ticket): TicketResource
    {
        $ticket = $this->ticketRepository->load($ticket, ['customer', 'media']);

        return new TicketResource($ticket);
    }

    public function store(TicketStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $ticket = $this->ticketService->createTicket(
                $validated,
                $request->file('attachments', [])
            );
        } catch (FileDoesNotExist|FileIsTooBig $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => (new TicketResource($ticket))->toArray($request),
        ], SymfonyResponse::HTTP_CREATED);
    }

    public function update(TicketUpdateStatusRequest $request, Ticket $ticket): TicketResource
    {
        $validated = $request->validated();
        $ticket = $this->ticketService->updateTicketStatus($ticket, $validated['status']);

        return new TicketResource($ticket);
    }

    public function statistics(): JsonResponse
    {
        $statistics = $this->ticketService->getTicketsStatistics();

        return response()->json([
            'data' => $statistics,
        ]);
    }
}
