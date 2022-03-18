<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user &&
            Hash::check($request->password, $user->password) && $user->hasVerifiedEmail()) {
            return $user;
        }
        if ($user &&
            Hash::check($request->password, $user->password) && ! $user->hasVerifiedEmail()) {
            return Response('email need to verify', 401);
        }
        return Response('Your email or password incorrect', 400);
    }
}
