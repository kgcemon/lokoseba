<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banner_images';
    protected $fillable = [
        'img_url',
    ];
}
