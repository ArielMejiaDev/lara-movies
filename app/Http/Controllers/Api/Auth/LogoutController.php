<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        auth('api')->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return Response([
            'message' => 'You are successfully logged out',
        ], 200);
    }
}
