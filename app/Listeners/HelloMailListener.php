<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class HelloMailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        sleep(20);
        Mail::to('samer@example.com')->send(new SendCreatePassword());

    }
}
