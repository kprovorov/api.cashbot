<?php

namespace App\Listeners;

use App\Http\Integrations\LogSnag\LogSnag;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserRegistered implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly LogSnag $logSnag)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $this->logSnag->log([
            'channel' => 'user-register',
            'event' => 'User Registered',
            'description' => "{$event->user->name} ({$event->user->email})",
            'icon' => '🤩',
            'notify' => true,
            'tags' => [
                'email' => $event->user->email,
                'uid' => $event->user->id,
            ],
        ]);
    }
}
