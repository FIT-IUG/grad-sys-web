<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewStudentHasCreateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student_email;
    public $key;

    /**
     * Create a new event instance.
     *
     * @param $student_email
     * @param $key
     */
    public function __construct($student_email, $key)
    {
        $this->student_email = $student_email;
        $this->key = $key;
    }

}
