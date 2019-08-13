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
                'showToken',
                'refreshToken',
                'update',
                'delete'
            ]
        ]);
        $this->middleware('credentials_auth', [
            'except' => [
                'register',
                'show',
                'showOptions',
                'updateOptions'
            ]
        ]);
    }

    /**
     * Register a new App\User
     * Require the field username, password.
     * Optional fields: options
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required|min:10'
        ]);

        $user = User::make([
            'username' => $request->input('username'),
            'options' => $request->input('options', '')
        ]);
        $user->password = Hash::make($request->input('password'));
        $user->generateToken();
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
    public function showToken()
    {
        // Return token
        return response()->json([
            'token' => Auth::user()->token,
        ], 200);
    }

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
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => 'min:10'
        ]);

        // Update the only 2 editable fields
        if ($request->has('password')) {
            Auth::user()->password = Hash::make($request->input('password'));
        }
        Auth::user()->options = $request->input('options', Auth::user()->options);
        Auth::user()->generateToken();
        // Save
        Auth::user()->save();

        return response()->json([
            'status' => 'User updated.',
            'options' => Auth::user()->options,
            'token' => Auth::user()->token
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

    /**
     * Display the saved options of an App\User
     */
    public function showOptions()
    {
        return response()->json([
            'options' => Auth::user()->options
        ], 200);
    }

    /**
     * Update the options of an App\User
     */
    public function updateOptions(Request $request)
    {
        Auth::user()->options = $request->input('options', Auth::user()->options);
        Auth::user()->save();

        return response()->json([
            'status' => 'Options saved.',
            'options' => Auth::user()->options
        ], 200);
    }
}