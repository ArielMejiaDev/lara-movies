<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'error' => [
                'message' => 'Email must be verified, you can resend email using the link below, the request require authentication.',
                'link' => route('verification.resend')
            ]
        ], 403);
    }
}
