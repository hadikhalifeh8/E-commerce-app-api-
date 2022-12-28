<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\items;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function getalldata()
    {
        $categories = categories::all();
        $items = items::where('discount','!=','0')->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories,
            'items' => $items,

        ]);
    }
}
