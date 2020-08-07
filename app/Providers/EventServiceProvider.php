<?php

namespace App\Providers;

use App\Events\NewStudentHasCreateEvent;
use App\Events\RestorePasswordEvent;
use App\Events\TestEvent;
use App\Events\UpdateDepartmentEvent;
use App\Events\UploadUsersExcelFileEvent;
use App\Listeners\CreateUserFromExcelListener;
use App\Listeners\HelloMailListener;
use App\Listeners\SendCreatePasswordEmailFromExcelListener;
use App\Listeners\SendCreatePasswordEmailListener;
use App\Listeners\SendRestorePasswordMailListener;
use App\Listeners\UpdateUsersDepartmentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UploadUsersExcelFileEvent::class => [
            CreateUserFromExcelListener::class,
            SendCreatePasswordEmailFromExcelListener::class,
        ], NewStudentHasCreateEvent::class => [
            SendCreatePasswordEmailListener::class
        ], RestorePasswordEvent::class => [
            SendRestorePasswordMailListener::class
        ], UpdateDepartmentEvent::class => [
            UpdateUsersDepartmentListener::class
        ],TestEvent::class=>[
            HelloMailListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
