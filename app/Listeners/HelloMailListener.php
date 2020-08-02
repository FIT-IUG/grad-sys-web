<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class HelloMailListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
//        sleep(10);
        Mail::to('osmaka1997@gmail.com')->send(new SendCreatePassword($event->token));

    }
}
