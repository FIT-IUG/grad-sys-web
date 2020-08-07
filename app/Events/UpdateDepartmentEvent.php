<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateDepartmentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $new_department_name;
    public string $old_department;

    /**
     * Create a new event instance.
     *
     * @param $new_department_name
     * @param $old_department
     */
    public function __construct($new_department_name, $old_department)
    {
        $this->new_department_name = $new_department_name;
        $this->old_department = $old_department;
    }

}
