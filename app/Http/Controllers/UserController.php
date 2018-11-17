<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('token_auth', [
            'except' => [
                'register',
                'login',
                'token',
                'refreshToken',
                'update',
                'delete',
            ]
        ]);
        $this->middleware('credentials_auth', [
            'except' => [
                'register',
                'show',
                'showOptions',
                'updateOptions',
            ]
        ]);
    }

    /**
     * Register a new App\User
     * Require the field username, password.
     * Optional fields: email, options
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required|min:10',
            'email' => 'nullable|email|unique:users'
        ]);

        // Set fields
        $user = new User();
        // Required fields
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->generateToken();
        // Optional fields
        $user->email = $request->input('email', null);
        $user->options = $request->input('options', '{}');
        // Save in DB
        $user->save();

        return response()->json([
            'status' => 'Account created.',
            'token' => $user->token
        ], 201);
    }

    public function login(Request $request) {
        return response()->json([
            'status' => 'Correct credentials.',
            'token' => Auth::user()->token,
        ], 200);
    }

    /**
     * Return the token of an App\User
     */
    /*public function token()
    {
        // Return token
        return response()->json([
            'token' => Auth::user()->token,
        ], 200);
    }*/

    /**
     * Generate a new token for an App\User
     */
    public function refreshToken()
    {
        Auth::user()->generateToken()->save();

        // Return token
        return response()->json([
            'status' => 'Token updated.',
            'token' => Auth::user()->token,
        ], 200);
    }

    /**
     * Update an App\User
     * The following fields won't be updated:
     *   id, username, token, creation_date, last_sync, last_update
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => 'min:10',
            'email' => [
                'nullable', 'email',
                Rule::unique('users')->ignore(Auth::user()->id)
            ]
        ]);

        // Save edited fields to output them
        $fields = [];

        // Update the only 3 editable fields
        if ($request->has('password')) {
            Auth::user()->password = Hash::make($request->input('password'));
        }
        Auth::user()->email = $request->input('email', Auth::user()->email);
        Auth::user()->options = $request->input('options', Auth::user()->options);

        // Save
        Auth::user()->save();

        return response()->json([
            'status' => 'User updated.'
        ], 200);
    }

    /**
     * Delete and App\User
     * All App\Title will also delete on cascade
     */
    public function delete()
    {
        Auth::user()->delete();

        return response()->json([
            'status' => 'User deleted.'
        ], 200);
    }

    /**
     * Display informations about an App\User
     */
    public function show()
    {
        return response()->json(Auth::user(), 200);
    }

    public function showOptions()
    {
        return response()->json([
            'options' => Auth::user()->options
        ], 200);
    }

    public function updateOptions(Request $request)
    {
        Auth::user()->options = $request->input('options', '{}');
        Auth::user()->save();

        return response()->json([
            'status' => 'Options saved.'
        ], 200);
    }
}