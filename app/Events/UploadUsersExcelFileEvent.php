<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadUsersExcelFileEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $users;

    /**
     * Create a new event instance.
     *
     * @param $users
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

}
