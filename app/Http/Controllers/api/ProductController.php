<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = $request->validate([
            'subcategory_id' => 'required|integer',
            'location' => 'required',
        ]);

        $user = $request->user();

        $Products = Product::where('location', $validate['location'])->
        where('subcategory_id',$validate['subcategory_id'])->
        with('user')->latest()->paginate(10);
        if ($Products->isEmpty()) {
            $Products = Product::where('area', $user->area)->
            where('subcategory_id',$validate['subcategory_id'])->
            with('user')->latest()->paginate(10);
        }

        return response()->json([
            'error' => false,
            'message' => 'Product List successfully found',
            'data' => $Products->items(),
            'pagination' => [
                'total' => $Products->total(),
                'per_page' => $Products->perPage(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $subcat = Subcategory::findOrFail($validate['subcategory_id']);
        $user = $request->user();

        // Handle image upload
        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images', $imageName);
            $path = Storage::url($path); // generates /storage/images/filename
        }

        $product = Product::create([
            'subcategory_id' => $validate['subcategory_id'],
            'category_id' => $subcat->category_id,
            'location' => $validate['location'],
            'description' => $validate['description'],
            'title' => $validate['title'],
            'image' => $path,
            'user_id' => $user->id,
            'area' => $user->area,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Product created successfully',
            'data' => $product->only(['id', 'title', 'description', 'location', 'image']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
