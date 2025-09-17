<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
