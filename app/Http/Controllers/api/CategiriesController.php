<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategiriesController extends Controller
{
    public function index(){
        $data = Category::with('subcategories')->get();
        return response()->json([
            'error' => false,
            'message' => 'success',
            'data' => $data
        ]);
    }
}
