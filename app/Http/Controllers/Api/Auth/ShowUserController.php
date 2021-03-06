<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowUserController extends Controller
{
    public function __invoke()
    {
        return Auth::user();
    }
}
