<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): Response
    {
        Auth::guard(config('fortify.guard'))->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.application.pending');
    }
}
