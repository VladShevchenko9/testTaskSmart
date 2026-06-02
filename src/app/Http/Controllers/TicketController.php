<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tickets = Ticket::with('customer')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('id')
            ->paginate(10);

        return TicketResource::collection($tickets);
    }

    public function show(Ticket $ticket): TicketResource
    {
        $ticket->load(['customer', 'media']);

        return new TicketResource($ticket);
    }

    public function update(Request $request, Ticket $ticket): TicketResource
    {
        $ticket->update([
            'status' => $request->status,
            'manager_reply_at' => now(),
        ]);

        return new TicketResource($ticket);
    }
}
