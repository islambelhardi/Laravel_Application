<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Announce;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AnnounceController extends Controller
{
    //
    public function index(){
        return Announce::all();
    }
    public function createannounce(Request $request){
        // handle request validation
        /*$this->validate($request->all(),[
            'title'=>'required|string',
            'description'=>'required|string|max:800',
            'dealtype'=>'required|string',
            'propretytype'=>'required|string',
            'roomnumber'=>'integer',
            'surface'=>'integer',
            'price'=>'integer',
        ]);*/
        $user_id=auth('api')->user()->id;
        $user = User::Where('id', $user_id)->first();
        $announce = new Announce();
        $details=[
            $announce->title=$request->title,
            $announce->description=$request->description,
            $announce->dealtype=$request->dealtype,
            $announce->propretytype=$request->propretytype,
            $announce->roomnumber=$request->roomnumber,
            $announce->surface=$request->surface,
            $announce->price=$request->price,
            $announce->place=$request->place,
            $announce->user_id=$user->id
        ];
        $announce->save($details);
        $destination_path=public_path('/AnnouncesImages/');
        if ($request->hasFile('images')){
            foreach($request->file('images') as $requestimage){
                $image = new Image;
                //to set the image name
                $image_name = '/AnnouncesImages/'.rand().'.'.$requestimage->getClientOriginalExtension();
                // store the images in the public path
                $requestimage->move($destination_path,$image_name);;
                // to set the image url in database
                $image->url=$image_name;
                //save the foreignkey
                $image->announce_id=$announce->id;
                $announce->img=$image_name;
                $announce->save();
                $image->save();
            }
            return response()->json('announce listed successfully',201);
        }else{
            return response()->json('please attach your images',);
        }
    }
    public function modifyannounce(Request $request){
        $announce_id=$request->id;
        $announce= Announce::where('id',$announce_id)->first();
        if (!$announce){
            return response()->json('Announce not found',404);
        }
        $changes=[$announce->title=$request->title,
        $announce->description=$request->description,
        $announce->dealtype=$request->dealtype,
        $announce->propretytype=$request->propretytype,
        $announce->roomnumber=$request->roomnumber,
        $announce->surface=$request->surface,
        $announce->price=$request->price,
        $announce->place=$request->place,
    ];
        $announce->save($changes);
        //delete the images passed as array
        if ($request->has('todeleteimages')){
           foreach ($request->todeleteimages as $image){
                //echo $image. "\n";
                $image_path=public_path("{$image}");
                if (File::delete($image_path)==true){
                    DB::table('images')
                        ->where('url','=',$image)->delete();
                    echo 'file deleted';
                }else{
                    echo 'no file to delete';
                }
            }
        }
        $destination_path=public_path('/AnnouncesImages/');
        // add the uploaded images
        if ($request->hasFile('uploaded_images')){
            foreach ($request->file('uploaded_images')as $requestimage){
                $image = new Image;
                //to set the image name
                $image_name = '/AnnouncesImages/'.rand().'.'.$requestimage->getClientOriginalExtension();
                // store the images in the public path
                $requestimage->move($destination_path,$image_name);
                // to set the image url in database
                $image->url=$image_name;
                //save the foreignkey
                $image->announce_id=$announce->id;
                $image->save();
                echo 'images uploaded';
            }
        }
        return response()->json('Announce edited ',200);
    }
    public function showannounce(Request $request){
        $announce_id=$request->id;
        $announce=Announce::where('id',$announce_id)->first();
        if (!$announce){
            return response()->json('announce not found',404);
        }
        $announce->images;
        $announce->agency;
        $announce->comments;
        return response()->json([$announce]);
    }
    public function deleteannounce(Request $request){
        $announce_id=$request->id;
        $announce = Announce::Where('id', $announce_id)->first();
        if (!$announce){
            return response()->json('announce not found',404);
        }
        $image = $announce->images;
        foreach ($image as $todeleteimage){
            $image_path=public_path("{$todeleteimage->url}");
            File::delete($image_path);
        }
        $announce->delete();
        return response()->json('announce deleted',200);
    }
    public function getrent(){
        $announces =DB::table('announces')->where('dealtype','rent')->get();
        return response()->json($announces);
    }
    public function getsell(){
        $announces =DB::table('announces')->where('dealtype','sale')->get();
        return response()->json($announces);
    }
    public function myannounces(){
        $agency_id= auth('api')->user()->id;
        $agency = User::Where('id',$agency_id)->first();
        $myannounces=$agency->announces;
        return response()->json($myannounces);
    }
}
