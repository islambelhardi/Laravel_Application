<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request  $request){
        // to get user id based on his token
        $user_id= auth('api')->user()->id;
        $user = User::Where('id',$user_id)->first();
        return response()->json($user);
    }
    //
    public function modifyinfo(Request $request){
        // to get user id based on his token
        $user_id= auth('api')->user()->id;
        $user = User::Where('id',$user_id)->first();
        $rules=['email' => 'string|unique:users'];
        $validator = Validator::make($request->all(),$rules );
        // first we need to verify the user password
        if ($user && Hash::check($request->password, $user->password)){
            // if the user change only his name
            if($user->email==$request->email &&$user->name!== $request->name){
                $user->name= $request->name;
                $user->save();
                return response()->json('username has changed', 200);
            }
            // change both use name and email
            if($user->email!==$request->email &&$user->name!== $request->name&& ! $validator->fails()){
                $user->name= $request->name;
                $user->email = $request->email;
                $user->email_verified_at=null;
                $user->save();
                event(new Registered($user));
                return response()->json('info updated verify your new email', 200);
            }// if user changes only his email
            elseif ($user->email!==$request->email&& ! $validator->fails()){
                $user->email = $request->email;
                $user->email_verified_at=null;
                $user->save();
                event(new Registered($user));
                return response()->json('email has changed verify your new email', 200);

            }else{
                // if the user try enter a taken email
                return response()->json('email already taken', 400);
            }

        }else{
            return response()->json('wrong password ',400);
        }
    }
    public function modifypassword(Request $request)
    {
        // to get user id based on his token
        $user_id = auth('api')->user()->id;
        $user = User::Where('id', $user_id)->first();
        // if user changes his password
        if ($user &&
            Hash::check($request->password, $user->password)  && $request->has('newpassword')) {
            $user->password = Hash::make($request->newpassword);
            $user->save();
            return response()->json('password changed ',200);
        }
    }
}
