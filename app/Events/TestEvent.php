<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $token;

    /**
     * Create a new event instance.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
}
