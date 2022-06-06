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
            return response()->json('email is already taken', 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type'=>$request->type,
        ]);
        event(new Registered($user));
        Auth::login($user);
        return response()->json('A Verification email sent confirm your email before login', 200);
    }
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        // if user is correct and has verified email
        if ($user &&
            Hash::check($request->password, $user->password) && $user->hasVerifiedEmail()) {
            $accesstoken = $user->createToken('access token')->accessToken;
            return response(['user'=>$user,'access token'=>$accesstoken]);
        }
        // if user is correct and hasn't verified email
        if ($user &&
            Hash::check($request->password, $user->password) && ! $user->hasVerifiedEmail()) {
            return response()->json('email need to verify',401);
        }
        // if user coordinate are wrong
        return response()->json('Your email or password incorrect',400);
    }
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json(
             'Successfully logged out',200
        );
    }
}
