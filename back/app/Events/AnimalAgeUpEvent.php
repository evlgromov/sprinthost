<?php

namespace App\Events;

use App\Models\Animal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnimalAgeUpEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $animal;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Animal $animal)
    {
        $this->queue = 'grow';
        $this->animal = $animal;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('animals');
    }

    public function broadcastAs() {
        return 'AnimalAgeUp';
    }
}
