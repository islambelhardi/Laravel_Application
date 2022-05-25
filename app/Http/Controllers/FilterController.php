<?php

namespace App\Http\Controllers;

use App\Models\Announce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    //

    public function filterannounces(Request $request){
        $announces = Announce::query();
        if (!empty($request->dealtype)){
            $announces->where('dealtype',$request->dealtype);
        }
        if (!empty($request->roomnumber)){
            $announces->where('roomnumber',$request->roomnumber);
        }
        if (!empty($request->minprice) && !empty($request->maxprice)){
            $announces->whereBetween('price',[$request->minprice,$request->maxprice]);
        }
        if (!empty($request->propretytype)){
            $announces->where('propretytype',$request->propretytype);
        }
        if (!empty($request->minsurface) && !empty($request->maxsurface)){
            $announces->whereBetween('surface',[$request->minsurface,$request->maxsurface]);
        }
        $results=$announces->get();
        if ($results->isEmpty()){
            return response()->json('no announces',404);
        }
        return $results;
    }
}
