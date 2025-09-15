<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name_bn',
        'name_en',
        'slug',
        'image',
        'status',
    ];
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}
