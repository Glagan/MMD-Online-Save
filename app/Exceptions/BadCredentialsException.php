<?php

namespace App\Exceptions;

use Exception;

class BadCredentialsException extends Exception
{
    // JSON Response
    static public function render()
    {
        return response()->json([
            'status' => 'Bad credentials.'
        ], 400);
    }
}