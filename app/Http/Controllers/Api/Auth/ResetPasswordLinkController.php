<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResetPasswordLinkController extends Controller
{
    public function __invoke(string $token)
    {
        return response()->json([
            'message' => 'You should add the token to the reset password POST request.',
            'token' => $token,
        ]);
    }
}
