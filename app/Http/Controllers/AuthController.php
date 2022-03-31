<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function create(){
        return view('SuccessfulEmailVerification');
    }
    public function register(Request $request){
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:8'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        event(new Registered($user));
        Auth::login($user);
        return Response('signup succes please verify your email', 200);
    }
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        // if user is correct and has verified email
        if ($user &&
            Hash::check($request->password, $user->password) && $user->hasVerifiedEmail()) {
            return $user;
        }
        // if user is correct and hasn't verified email
        if ($user &&
            Hash::check($request->password, $user->password) && ! $user->hasVerifiedEmail()) {
            //return Response('email need to verify', 401);
            return response()->json('email need to verify',401);
        }
        // if user coordinate are wrong
        //return Response('Your email or password incorrect', 400);
        return response()->json('Your email or password incorrect',400);
    }
}
