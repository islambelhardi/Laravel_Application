<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'content','user_id','announce_id','username'
    ];
    protected $hidden = [
        'user_id',
    ];
    public function announce()
    {
        return $this->belongsTo('App\Models\Announce', 'announce_id');
    }
}
