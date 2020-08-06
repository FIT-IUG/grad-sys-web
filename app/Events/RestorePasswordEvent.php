<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestorePasswordEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $email;

    /**
     * Create a new event instance.
     *
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

}
