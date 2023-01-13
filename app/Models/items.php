<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class items extends Model
{
    use HasFactory;

    protected $table = 'items';
    
    protected $guarded=[];



     // relation between Table items(category_id) && Table categories(id) to get the category name by id 
    public function category_rltn()
    {
        return $this->belongsTo('App\Models\categories', 'category_id');
    }


  

      // علاقة المستخدمين مع الأصناف 
      public function users_rltn()
      {
           //                               model       ,table_name / pivote Table
          return $this->belongsToMany('App\Models\User','favorite');
      }
}
