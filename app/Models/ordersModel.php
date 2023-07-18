<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordersModel extends Model
{
    use HasFactory;

    protected $table = 'orders';
    
    protected $guarded=[];



         
         public function usr_rltn()
         {
             return $this->belongsTo('App\Models\User', 'user_id');
         }


         public function address_rltn()
         {
             return $this->belongsTo('App\Models\address', 'address_id');
         }


         public function coupon_rltn()
         {
             return $this->belongsTo('App\Models\couponModel', 'used_coupon');
         }

         public function delivery_rltn()
         {
             return $this->belongsTo('App\Models\delivery_users', 'delivery_id');
         }
}
