<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    use HasFactory;

    protected $table = 'address';
    
    protected $guarded=[];


    public function user_rltn()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}


