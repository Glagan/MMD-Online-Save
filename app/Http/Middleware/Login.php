<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Exceptions\BadCredentialsException;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('X-Auth-Name') && $request->header('X-Auth-Pass')) {
            try {
                Validator::validate(
                [
                    'X-Auth-Name' => $request->header('X-Auth-Name'),
                    'X-Auth-Pass' => $request->header('X-Auth-Pass')
                ], [
                    'X-Auth-Name' => 'required',
                    'X-Auth-Pass' => 'required|min:10'
                ]);

                // Find username
                $user = User::where('username', '=', $request->header('X-Auth-Name'))->first();

                // Verify Hash
                if ($user == null || !Hash::check($request->header('X-Auth-Pass'), $user->password)) {
                    throw new BadCredentialsException(); // Return 'Bad Credentials' if not logged in
                }

                // Add the user to the Auth Facade
                Auth::setUser($user);

                return $next($request);
            } catch (ValidationException | BadCredentialsException $e) {
                return BadCredentialsException::render();
            }
        }

        return BadCredentialsException::render();
    }
}
