<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request)
    {
        $token = $request->get('token');

        if(! $passwordResets = DB::table('password_resets')->where('token', $token)->first()) {
            return response()->json([
                'message' => 'invalid token',
            ], 400);
        }

        /** @var User $user */
        if(! $user = User::where('email', $passwordResets->email)->first()) {
            return response()->json([
                'message' => 'User does not exists',
            ], 404);
        }

        $user->update(['password' => bcrypt($request->get('password'))]);

        return response()->json([
            'message' => 'password updated',
        ]);
    }
}
