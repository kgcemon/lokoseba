<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
