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

    public function modifyphoto(Request $request){
        //get the agency id based on their token
        $agency_id=auth('api')->user()->id;
        $agency = Agency::Where('id', $agency_id)->first();
        // to validate that the request has a valid image
        $request->validate([
            'photo' => 'required|mimes:jpeg,png,jpg,svg|max:5048',
        ]);
        // if the agency doesnt have a photo already
        if ($agency->image==null ){
            $image=$request->file('photo');
            //specify the storage path
            $destination_path=public_path('/Agency/photos');
            //generate a name for the image
            $image_name = time().'.'.$image->getClientOriginalExtension();
            // upload the image to the desi
            $request->photo->move($destination_path,$image_name);
            $agency->image=$image_name;
            $agency->save();
            return response()->json('image uploaded seccessfully',200);
        }
        // if they want to change their existing photo
        else{
            $image=$agency->image;
            $image_path=public_path("/Agency/photos/{$image}");
            // delete the previous image
            File::delete($image_path);
            $image=$request->file('photo');
            //specify the storage path
            $destination_path=public_path('/Agency/photos');
            //generate a name for the image
            $image_name = time().'.'.$image->getClientOriginalExtension();
            // upload the image to the desi
            $request->photo->move($destination_path,$image_name);
            $agency->image=$image_name;
            $agency->save();
            return response()->json('image changed seccessfully',200);
        }
    }

    public function modifyphone(Request $request){
        // to get user id based on his token
        $agency_id= auth('api')->user()->id;
        $agency = Agency::Where('id',$agency_id)->first();
        $request->validate([
            'phone_number' => 'required|integer',
        ]);
        $agency->phone_number=$request->phone_number;
        $agency->save();
        return response()->json('phone number changed ', 200);
    }
}
