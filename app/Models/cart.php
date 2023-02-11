<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;

    protected $table = '_carts';
    protected $fillable=['users_id','items_id'];


    
    public function user_rltn()
    {
        return $this->belongsTo('App\Models\User', 'users_id');
    }


    public function item_rltn()
    {
        return $this->belongsTo('App\Models\items', 'items_id');
    }
}
