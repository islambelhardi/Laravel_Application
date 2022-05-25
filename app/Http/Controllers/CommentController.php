<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //
    public function create(Request $request){
        $user_id= auth('api')->user()->id;
        $username=auth('api')->user()->name;
        $comment = new Comment();
        $comment->content=$request->comment;
        $comment->announce_id=$request->announce_id;
        $comment->user_id=$user_id;
        $comment->username=$username;
        if ($comment->save()) {
            return response()->json('successfully commented', 200);
        } else {
            return response()->json('something went wrong', 400);
        }
    }
}
