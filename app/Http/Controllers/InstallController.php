<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
    public function install(Request $request)
    {
        if (env('INSTALL_TOKEN', 'none') != 'none' &&
            $request->header('X-Auth-Token') == env('INSTALL_TOKEN', '')) {
            return Artisan::call('migrate');
        } else {
            return response()->json([
                'status' => 'Unauthorized.'
            ], 401);
        }
    }
}
