<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
    public function install(Request $request)
    {
        // Only start if a INSTALL_TOKEN is set and the received one is correct
        if (env('INSTALL_TOKEN', 'none') == 'none' ||
            $request->header('X-Auth-Token') != env('INSTALL_TOKEN')) {
            return response()->json([
                'status' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'artisan' => Artisan::call('migrate')
        ], 201);
    }
}
