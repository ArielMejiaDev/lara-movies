<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
//use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $successfullyVerifiedUrl = 'https://howtojsonapi.com/vue.html';

    protected $alreadyVerifiedUrl = 'https://howtojsonapi.com/react.html';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if(is_null($request->user('api'))) {
            return response()->json([
                'error' => [
                    'message' => 'This route requires authentication.'
                ]
            ], 401);
        }

        if ($request->user('api')->hasVerifiedEmail()) {

            if($request->wantsJson()) {
                return response(['message'=>'Already verified']);
            }

            return redirect()->to($this->alreadyVerifiedUrl ?? $this->successfullyVerifiedUrl);
        }

        $request->user('api')->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email Sent',
        ], 200);
    }


    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        auth()->loginUsingId($request->route('id'));

        if ($request->route('id') != $request->user()->getKey()) {

            throw new AuthorizationException;

        }

        if ($request->user()->hasVerifiedEmail()) {
            if($request->wantsJson()) {

                return response(['message'=>'Already verified']);

            }
            return redirect()->to($this->alreadyVerifiedUrl ?? $this->successfullyVerifiedUrl);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        if($request->wantsJson()) {

            return response(['message'=>'Successfully verified']);

        }
        return redirect()->to($this->successfullyVerifiedUrl);
    }


}
