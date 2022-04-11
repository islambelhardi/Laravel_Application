<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announce extends Model
{
    use HasFactory;
    protected $fillable=[
        'title','description','dealtype','propretytype','roomnumber'
        ];
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'announce_id');
    }
}
