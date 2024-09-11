<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class newBatchProcessRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $batchProcessArr = [];

    
    /**
     * Create a new event instance.
     */
    public function __construct($batchProcessArr) {
        $this->batchProcessArr = $batchProcessArr ?? [];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
