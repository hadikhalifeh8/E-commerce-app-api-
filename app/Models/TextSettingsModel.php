<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextSettingsModel extends Model
{
    use HasFactory;

    protected $table = 'textsetting';
    
    protected $guarded=[];
}
