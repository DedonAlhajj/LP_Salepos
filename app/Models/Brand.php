<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable =[

        "title", "image", "page_title", "short_description", "slug", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
}
