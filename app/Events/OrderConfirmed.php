<?php

namespace App\Events;

use App\Models\Order;
use App\Models\PrintJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  Collection<int, \App\Models\OrderItem>  $items
     * @param  PrintJob[]  $printJobs
     */
    public function __construct(
        public readonly Order $order,
        public readonly Collection $items,
        public readonly array $printJobs,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('orders')];
    }

    public function broadcastAs(): string
    {
        return 'order.confirmed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'   => $this->order->id,
            'table'      => $this->order->table->number,
            'waiter'     => $this->order->waiter->name,
            'print_jobs' => collect($this->printJobs)->map(fn (PrintJob $job) => [
                'id'      => $job->id,
                'payload' => $job->payload,
            ])->values()->all(),
        ];
    }
}
