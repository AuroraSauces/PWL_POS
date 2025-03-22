<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // All your properties should be inside this class definition
    protected $middlewareAliases = [
        'auth'         => \App\Http\Middleware\Authenticate::class,
        'authorize'    => \App\Http\Middleware\AuthorizeUser::class,
        'auth.basic'   => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
    ];
}
