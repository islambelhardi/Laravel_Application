<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Announce extends Model
{
    use HasFactory;
    protected $fillable=[
        'title','description','dealtype','propretytype','roomnumber','place','price','surface'
        ];

    public function agency()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'announce_id');
    }
    public function comments(){
        return $this->hasMany('App\Models\Comment','announce_id');
    }
}
