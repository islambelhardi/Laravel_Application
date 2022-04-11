<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'images';
    protected $fillable = [
        'url', 'announce_id'
    ];
    public function announce()
    {
        return $this->belongsTo('App\Models\Announce', 'announce_id');
    }
}
