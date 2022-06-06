<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        if ($user->email!==$request->email){
            $rules=['email' => 'string|unique:users'];
            $validator = Validator::make($request->all(),$rules );
        }
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
            elseif ($user->email!==$request->email&& !$validator->fails()){
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
    public function modifyphoto(Request $request){
        //get the agency id based on their token
        $user_id=auth('api')->user()->id;
        $user = User::Where('id', $user_id)->first();
        // to validate that the request has a valid image
        $request->validate([
            'photo' => 'required|mimes:jpeg,png,jpg,svg|max:5048',
        ]);
        // if the agency doesnt have a photo already
        if ($user->image==null ){
            $image=$request->file('photo');
            //specify the storage path
            $destination_path=public_path('/Agency/photos');
            //generate a name for the image
            $image_name = '/Agency/photos/'.rand().'.'.$image->getClientOriginalExtension();
            // upload the image to the desi
            $request->photo->move($destination_path,$image_name);
            $user->image=$image_name;
            $user->save();
            return response()->json('image uploaded seccessfully',200);
        }
        // if they want to change their existing photo
        else{
            $image=$user->image;
            $image_path=public_path("{$image}");
            echo $image_path;
            // delete the previous image
            File::delete($image_path);
            $image=$request->file('photo');
            //specify the storage path
            $destination_path=public_path('/Agency/photos');
            //generate a name for the image
            $image_name = '/Agency/photos/'.rand().'.'.$image->getClientOriginalExtension();
            // upload the image to the desi
            $request->photo->move($destination_path,$image_name);
            $user->image=$image_name;
            $user->save();
            return response()->json('image changed seccessfully',200);
        }
    }
    public function modifyphone(Request $request){
        // to get user id based on his token
        $user_id= auth('api')->user()->id;
        $agency = User::Where('id',$user_id)->first();
        $request->validate([
            'phone_number' => 'required|integer',
        ]);
        $agency->phone_number=$request->phone_number;
        $agency->save();
        return response()->json('phone number changed ', 200);
    }
}
