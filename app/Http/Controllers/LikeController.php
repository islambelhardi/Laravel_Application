<?php

namespace App\Http\Controllers;

use App\Models\Announce;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user_id = auth('api')->user()->id;
        $user =User::Where('id',$user_id)->first();
        $likesarray=$user->likes->toarray();
        if (count($likesarray)==0){
            return response()->json('you haven\'t liked any announces yet',400);
        }
        $announces_id =array();
        for ($i=0; $i<count($likesarray);$i++){
            $announces_id[] = $likesarray[$i]['announce_id'];
        }
        $announces=array();
        for ($i=0; $i<count($announces_id);$i++){
            $announce = new  Announce();
            $announce =  $announce::Where('id',$announces_id[$i])->first();
           $announces[]=$announce;
        }
        return response()->json($announces,200);
        }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *@return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = auth('api')->user()->id;
        //$user =User::Where('id',$user_id)->first();
        //$announce = new Announce();
        $announce_id=$request->announce_id;
        $like=new Like();
        $like->user_id=$user_id;
        $like->announce_id=$announce_id;
        try {
            $like->save();
        }catch (Throwable $e){
            report($e);
        }

        return response()->json('announce liked ',200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function destroy(Like $like)
    {
        //
    }
}
