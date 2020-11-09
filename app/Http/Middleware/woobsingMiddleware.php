<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class woobsingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::user()->id;
        $boolEmailVerified = $this->checkEmailVerified($userId);
        if(!$boolEmailVerified){
            return redirect('/verificacion');
        }
        $boolLastSessionMoreThan24Hours = $this->checkLastSession($userId);
        if(!$boolLastSessionMoreThan24Hours){
            return redirect('/sesiones');
        }
        if($request->ip() == '127.0.0.1' && Auth::User()->role == 1){
            $user = auth()->user();
            $user->two_factor_token = str_random(10);
            $user->save();
            \Mail::to($user)->send(new TwoFactorAuthMail($user->two_factor_token));
            return redirect('/2fa');
        }
        return $next($request);
    }

    private function checkEmailVerified($userId){
        $user = User::find($userId);
        if(!$user->email_verified_at){
            return false;
        }
        return true;
    }

}
