<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\items;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    public function offers()
    {
        $offers = items::with('category_rltn')->where('discount','!=','0')->get();

        if($offers->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => $offers,
                ]);
        }else{
            return response()->json([
                               'status' => 'failure',
                                'data' => 'No offers items Found',
                            ]);
        }

    }
}
