<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AgencyController extends Controller
{
    public function modifyinfo(Request $request){
        // to get user id based on his token
        $agency_id= auth('api')->user()->id;
        $agency = Agency::Where('id',$agency_id)->first();
        $rules=['email' => 'string|unique:agencies'];
        $validator = Validator::make($request->all(),$rules );
        // first we need to verify the user password
        if ($agency && Hash::check($request->password, $agency->password)){
            // if the user change only his name
            if($agency->email==$request->email &&$agency->name!== $request->name){
                $agency->name= $request->name;
                $agency->save();
                return response()->json('username has changed', 200);
            }
            // if user changes only his email
            if($agency->email!==$request->email&& ! $validator->fails()){
                $agency->email = $request->email;
                $agency->email_verified_at=null;
                $agency->save();
                event(new Registered($agency));
                return response()->json('email has changed verify your new email', 200);
            }else{
                // if the user try enter a taken email
                return response()->json('email already taken', 400);
            }
        }else{
            return response()->json('wrong password ',400);
        }
    }
    public function modifypassword(Request $request){
        // to get user id based on his token
        $agency_id= auth('api')->user()->id;
        $agency = Agency::Where('id',$agency_id)->first();
        if ($agency &&
            Hash::check($request->password, $agency->password)  && $request->has('newpassword')) {
            $agency->password = Hash::make($request->newpassword);
            $agency->save();
            return response()->json('password changed ',200);
        }
    }
    /**
     * @throws \Illuminate\Validation\ValidationException
     */




}
