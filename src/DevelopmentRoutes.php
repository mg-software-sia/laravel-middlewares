<?php

namespace MgSoftware\Middlewares;

use Illuminate\Http\Request;

class DevelopmentRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $authenticationHasPassed = false;

        if (app()->environment('local')) {
            $authenticationHasPassed = true;
        } elseif ($request->header('PHP_AUTH_USER', null) && $request->header('PHP_AUTH_PW', null)) {
            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
            $compareUsername = env('DEVELOPER_BASIC_AUTH_USERNAME', md5(random_int(1, 99999)));
            $comparePassword = env('DEVELOPER_BASIC_AUTH_PASSWORD', md5(random_int(1, 99999)));

            if ($username === $compareUsername && $password === $comparePassword) {
                $authenticationHasPassed = true;
            }
        }

        if ($authenticationHasPassed === false) {
            return response()->make('Invalid credentials.', 401, ['WWW-Authenticate' => 'Basic']);
        }

        return $next($request);
    }
}
