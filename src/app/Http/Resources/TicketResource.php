<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'manager_reply_at' => $this->manager_reply_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),

            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'email' => $this->customer?->email,
            ],

            'attachments' => $this->getMedia('attachments')->map(fn($m) => [
                'url' => $m->getUrl(),
                'file_name' => $m->file_name,
            ]),
        ];
    }
}
