<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery_users extends Model
{
    use HasFactory;

    protected $table = 'delivery__users';
    
    protected $guarded=[];
}
