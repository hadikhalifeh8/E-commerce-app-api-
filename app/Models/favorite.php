<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favorite extends Model
{
    use HasFactory;

    protected $table = 'favorite';
    protected $fillable=['users_id','items_id'];


         // relation between Table items(category_id) && Table categories(id) to get the category name by id 
         public function user_rltn()
         {
             return $this->belongsTo('App\Models\User', 'users_id');
         }


              // relation between Table items(category_id) && Table categories(id) to get the category name by id 
    public function item_rltn()
    {
        return $this->belongsTo('App\Models\items', 'items_id');
    }
}
