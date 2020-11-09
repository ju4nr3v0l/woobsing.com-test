<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class fireLoginEvents
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        Event::listen('auth.login', function($user)
        {
            $now = new \DateTime('now');
            $lastLogin = $user->last_login;
            $diff = $now->diff($lastLogin);
            $hours = $diff->h;
            $hours = $hours + ($diff->days*24);
            $user->last_login = new DateTime;
            $user->save();
            if($hours > 24){
                return redirect('/sesiones');
            }
        });
    }
}
