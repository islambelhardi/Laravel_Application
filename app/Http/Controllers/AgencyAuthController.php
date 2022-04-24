<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AgencyAuthController extends Controller
{
    public function register(Request $request){
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:agencies',
            'password' => 'required|string|min:8',
            'phone_number'=>'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json('email is already taken', 400);
        }
        $agency = Agency::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number'=> $request->phone_number,
        ]);
        event(new Registered($agency));
        return response()->json('A Verification email sent confirm your email before login', 200);
    }
    public function login(Request $request)
    {
        $agency = Agency::where('email', $request->email)->first();
        // if user is correct and has verified email
        if ($agency &&
            Hash::check($request->password, $agency->password) && $agency->hasVerifiedEmail()) {
            $accesstoken = $agency->createToken('access token')->accessToken;
            return response(['user'=>$agency,'access token'=>$accesstoken]);
        }
        // if user is correct and hasn't verified email
        if ($agency &&
            Hash::check($request->password, $agency->password) && ! $agency->hasVerifiedEmail()) {
            //return Response('email need to verify', 401);
            return response()->json('email need to verify',401);
        }
        // if user coordinate are wrong
        //return Response('Your email or password incorrect', 400);
        return response()->json('Your email or password incorrect',400);
    }
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json(
            'Successfully logged out',200
        );
    }
}
