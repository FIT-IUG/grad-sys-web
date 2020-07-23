<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewStudentHasCreateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student;

    /**
     * Create a new event instance.
     *
     * @param $student
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

}
