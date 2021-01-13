<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        $email = $request->get('email');

        if(User::where('email', $email)->doesntExist()) {
            return response()->json([
                'message' => 'User does not exists',
            ], 404);
        }

        $token = Str::random(10);

        try {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
            ]);

            // send email
            Notification::route('mail', $email)->notify(new ForgotPasswordNotification($token));

            return response()->json([
                'message' => 'check your email',
            ]);

        } catch(\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        }

    }
}
