<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,

            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],

            'attachments' => $this->getMedia('attachments')->map(fn($m) => [
                'url' => $m->getUrl(),
                'file_name' => $m->file_name,
            ]),
        ];
    }
}
